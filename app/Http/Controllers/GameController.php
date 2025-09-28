<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\Player;
use App\Models\Competition;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request, Competition $competition = null)
    {
        if ($competition) {
            // Determine season: prefer explicit season query param, otherwise use competition->season
            $season = null;
            if ($request->filled('season')) {
                $season = \App\Models\Season::find($request->query('season'));
            }
            if (! $season) {
                $season = $competition->season;
            }

            // Load games for this competition, ordered by date and paginate for AJAX navigation
            $perPage = 3; // match the season view's chunk size
            $gamesQuery = $competition->games()->with(['homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])->orderBy('date');
            $games = $gamesQuery->paginate($perPage)->appends($request->query());

            // If this is an AJAX request (fetch from the seasons page), return only the rendered list partial
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return view('competitions.games._list', compact('competition', 'season', 'games'))->render();
            }

            return view('competitions.games.index', compact('competition', 'season', 'games'));
        }

        $games = Game::with(['competition.season', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])->latest('date')->paginate(20);

        // Prepare grouped structure for the view: seasons => competitions => collection of games
        $collection = $games->getCollection();
        $groupedBySeason = $collection->groupBy(function ($g) {
            return optional($g->competition->season)->name ?? 'Unassigned Season';
        })->map(function ($seasonGames) {
            return $seasonGames->groupBy(function ($g) {
                return $g->competition->name ?? '—';
            });
        });

        // Pass grouped data and the paginator (for links)
        return view('games.index', [
            'games' => $games,
            'groupedGames' => $groupedBySeason,
        ]);
    }

    public function create()
    {
        $competitions = Competition::all();
        $seasons = \App\Models\Season::orderBy('start_date', 'desc')->get();
        $currentSeason = \App\Models\Season::where('current', true)->first() ?? $seasons->first();

        // Limit teams to those in the current season by default
        $teams = Team::where('season_id', $currentSeason?->id)->orderBy('name')->get();
        $players = Player::orderBy('name')->get();

        // Also expose the season-scoped teams as a separate variable for the form
        $seasonTeams = $teams;

        return view('games.create', compact('competitions', 'seasons', 'currentSeason', 'teams', 'players', 'seasonTeams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'competition_id' => 'required|string|exists:competitions,id',
            'home_team_id' => 'nullable|string|exists:teams,id',
            'away_team_id' => 'nullable|string|exists:teams,id',
            'home_indiv_id' => 'nullable|string|exists:players,id',
            'away_indiv_id' => 'nullable|string|exists:players,id',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'date' => 'nullable|date',
        ]);

        // Enforce mutual exclusivity / required fields by competition type
        $this->validateByCompetitionType($data, $request);

        $game = Game::create($data);

        return redirect()->route('games.show', $game)->with('success', 'Game created');
    }

    public function show(Game $game)
    {
        $game->load(['competition', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer']);
        return view('games.show', compact('game'));
    }

    public function edit(Game $game)
    {
        $competitions = Competition::all();
        $seasons = \App\Models\Season::orderBy('start_date', 'desc')->get();
        $currentSeason = \App\Models\Season::where('current', true)->first() ?? $seasons->first();

        // For edit, default team lists to the game's season if available, otherwise current
        $seasonId = $game->season_id ?? $currentSeason?->id;
        $teams = Team::where('season_id', $seasonId)->orderBy('name')->get();
        $players = Player::orderBy('name')->get();

        $seasonTeams = $teams;

        return view('games.edit', compact('game', 'competitions', 'seasons', 'currentSeason', 'teams', 'players', 'seasonTeams'));
    }

    public function update(Request $request, Game $game)
    {
        $data = $request->validate([
            'competition_id' => 'required|string|exists:competitions,id',
            'home_team_id' => 'nullable|string|exists:teams,id',
            'away_team_id' => 'nullable|string|exists:teams,id',
            'home_indiv_id' => 'nullable|string|exists:players,id',
            'away_indiv_id' => 'nullable|string|exists:players,id',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'date' => 'nullable|date',
        ]);

        // Enforce mutual exclusivity / required fields by competition type
        $this->validateByCompetitionType($data, $request);

        $game->update($data);

        return redirect()->route('games.show', $game)->with('success', 'Game updated');
    }

    /**
     * Validate fields depending on competition type.
     * Modifies $data by reference (not strictly required) and throws ValidationException on failure.
     */
    protected function validateByCompetitionType(array &$data, Request $request)
    {
        $competition = Competition::find($data['competition_id']);
        if (! $competition) {
            return; // base validation already ensures existence
        }

        $type = $competition->type;

        if (in_array($type, ['team_league', 'team_cup'])) {
            // require teams, forbid individual players
            $request->validate([
                'home_team_id' => 'required|string|exists:teams,id',
                'away_team_id' => 'required|string|exists:teams,id',
                'home_indiv_id' => 'nullable|prohibited',
                'away_indiv_id' => 'nullable|prohibited',
            ]);
        } elseif ($type === 'individ_cup') {
            // require individuals, forbid teams
            $request->validate([
                'home_indiv_id' => 'required|string|exists:players,id',
                'away_indiv_id' => 'required|string|exists:players,id',
                'home_team_id' => 'nullable|prohibited',
                'away_team_id' => 'nullable|prohibited',
            ]);
        } else {
            // For unknown types: forbid mixing teams and players in the same record
            $hasTeams = !empty($data['home_team_id']) || !empty($data['away_team_id']);
            $hasPlayers = !empty($data['home_indiv_id']) || !empty($data['away_indiv_id']);
            if ($hasTeams && $hasPlayers) {
                $request->validate([
                    'home_indiv_id' => 'nullable|prohibited',
                    'away_indiv_id' => 'nullable|prohibited',
                ]);
            }
        }
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->route('games.index')->with('success', 'Game deleted');
    }
}
