<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::with('season')->paginate(20);

        return view('competitions.index', compact('competitions'));
    }

    public function create()
    {
        $seasons = Season::orderBy('start_date', 'desc')->get();
        $types = ['team_league', 'team_cup', 'individ_cup', 'pairs_cup'];

        return view('competitions.create', compact('seasons', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'season_id' => ['required', 'string', 'exists:seasons,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:team_league,team_cup,individ_cup,pairs_cup'],
        ]);

        $competition = new Competition($data);
        if (empty($competition->getKey())) {
            $competition->{$competition->getKeyName()} = (string) Str::ulid();
        }
        $competition->save();

        return redirect()->route('competitions.index')->with('success', 'Competition created');
    }

    public function show(Competition $competition)
    {
        $competition->load('season');

        $standings = null;

        // If this is a team league, compute a simple standings table from completed games.
        if ($competition->type === 'team_league') {
            $games = $competition->games()->with(['homeTeam', 'awayTeam'])->get();

            $table = [];

            // Helper to ensure team row exists
            $ensureTeam = function ($team) use (&$table) {
                if (!$team) return;
                $id = $team->getKey();
                if (!isset($table[$id])) {
                    $table[$id] = [
                        'team_id' => $id,
                        'name' => $team->name,
                        'played' => 0,
                        'wins' => 0,
                        'draws' => 0,
                        'losses' => 0,
                        'for' => 0,
                    ];
                }
            };

            foreach ($games as $game) {
                // Only consider games between teams (team fixtures)
                if (!$game->homeTeam || !$game->awayTeam) continue;

                // If scores are not set, skip for standings
                if ($game->home_score === null || $game->away_score === null) continue;

                $ensureTeam($game->homeTeam);
                $ensureTeam($game->awayTeam);

                $homeId = $game->homeTeam->getKey();
                $awayId = $game->awayTeam->getKey();

                // Update played and goals
                $table[$homeId]['played']++;
                $table[$awayId]['played']++;

                $table[$homeId]['for'] += $game->home_score;
                $table[$awayId]['for'] += $game->away_score;

                // Decide result: win/draw/loss. Use standard 3-1-0 points.
                if ($game->home_score > $game->away_score) {
                    $table[$homeId]['wins']++;
                    $table[$awayId]['losses']++;
                } elseif ($game->home_score < $game->away_score) {
                    $table[$awayId]['wins']++;
                    $table[$homeId]['losses']++;
                } else {
                    $table[$homeId]['draws']++;
                    $table[$awayId]['draws']++;
                }
            }

            // Convert to list and sort by points, then goal difference, then goals for
            $standings = array_values($table);
            // Order by goals for (GF) descending, then wins descending, then fewer played
            usort($standings, function ($a, $b) {
                if ($a['for'] !== $b['for']) return $b['for'] <=> $a['for'];
                if ($a['wins'] !== $b['wins']) return $b['wins'] <=> $a['wins'];
                return $a['played'] <=> $b['played'];
            });
        }

        return view('competitions.show', compact('competition', 'standings'));
    }

    public function edit(Competition $competition)
    {
        $seasons = Season::orderBy('start_date', 'desc')->get();
        $types = ['team_league', 'team_cup', 'individ_cup', 'pairs_cup'];

        return view('competitions.edit', compact('competition', 'seasons', 'types'));
    }

    public function update(Request $request, Competition $competition)
    {
        $data = $request->validate([
            'season_id' => ['required', 'string', 'exists:seasons,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:team_league,team_cup,individ_cup,pairs_cup'],
        ]);

        $competition->update($data);

        return redirect()->route('competitions.show', $competition)->with('success', 'Competition updated');
    }

    public function destroy(Competition $competition)
    {
        $competition->delete();

        return redirect()->route('competitions.index')->with('success', 'Competition deleted');
    }
}
