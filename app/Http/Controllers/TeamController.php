<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /** Display a listing of the teams. */
    public function index()
    {
        $teams = Team::with('season')->orderBy('name')->paginate(15);
        return view('teams.index', compact('teams'));
    }

    /** Show the form for creating a new team. */
    public function create()
    {
        $seasons = Season::orderBy('start_date', 'desc')->get();
        return view('teams.create', compact('seasons'));
    }

    /** Store a newly created team in storage. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'season_id' => 'required|exists:seasons,id',
        ]);

        DB::transaction(function () use ($data, &$team) {
            $team = new Team($data);
            if (empty($team->getKey())) {
                $team->{$team->getKeyName()} = (string) Str::ulid();
            }
            $team->save();
        });

        return redirect()->route('teams.index')->with('success', 'Team created.');
    }

    /** Display the specified team. */
    public function show(Team $team)
    {
        $team->load('season');

        // Load player_team records for this team filtered to the team's season when available
        if ($team->season) {
            $seasonStart = $team->season->start_date;
            $seasonEnd = $team->season->end_date;

            // Overlap logic: (start_date is null or start_date <= seasonEnd) AND (end_date is null or end_date >= seasonStart)
            $playerTeams = $team->playerTeams()
                ->where(function ($q) use ($seasonEnd) {
                    $q->whereNull('start_date')
                      ->orWhereDate('start_date', '<=', $seasonEnd);
                })
                ->where(function ($q) use ($seasonStart) {
                    $q->whereNull('end_date')
                      ->orWhereDate('end_date', '>=', $seasonStart);
                })
                ->with('player')
                ->get();
        } else {
            $playerTeams = $team->playerTeams()->with('player')->get();
        }

        // Load all games for this team (home or away) so the view can show fixtures and results
        $gameQuery = \App\Models\Game::with(['competition', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
            ->where(function ($q) use ($team) {
                $q->where('home_team_id', $team->getKey())
                  ->orWhere('away_team_id', $team->getKey());
            });

        // If the team belongs to a season, limit games to competitions in that season
        if ($team->season_id) {
            $seasonId = $team->season_id;
            $gameQuery->whereHas('competition', function ($q) use ($seasonId) {
                $q->where('season_id', $seasonId);
            });
        }

        $fixtures = $gameQuery->orderBy('date')->get();

        return view('teams.show', compact('team', 'fixtures', 'playerTeams'));
    }

    /** Show the form for editing the specified team. */
    public function edit(Team $team)
    {
        $seasons = Season::orderBy('start_date', 'desc')->get();
        return view('teams.edit', compact('team', 'seasons'));
    }

    /** Update the specified team in storage. */
    public function update(Request $request, Team $team)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'season_id' => 'required|exists:seasons,id',
        ]);

        $team->update($data);

        return redirect()->route('teams.index')->with('success', 'Team updated.');
    }

    /** Remove the specified team from storage. */
    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted.');
    }
}
