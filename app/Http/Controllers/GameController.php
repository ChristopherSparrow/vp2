<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\Player;
use App\Models\Competition;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with(['competition', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])->latest('date')->paginate(20);
        return view('games.index', compact('games'));
    }

    public function create()
    {
        $competitions = Competition::all();
        $teams = Team::orderBy('name')->get();
        $players = Player::orderBy('name')->get();
        return view('games.create', compact('competitions', 'teams', 'players'));
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
        $teams = Team::orderBy('name')->get();
        $players = Player::orderBy('name')->get();
        return view('games.edit', compact('game', 'competitions', 'teams', 'players'));
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
