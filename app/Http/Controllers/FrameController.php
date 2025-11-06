<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;

class FrameController extends Controller
{
    public function index(Request $request)
    {
        $frames = Frame::with(['game', 'homePlayer', 'awayPlayer'])->latest('created_at')->paginate(30);
        return view('frames.index', compact('frames'));
    }

    public function create(Request $request)
    {
        // Only allow association with games where competition type starts with 'team'
        $games = Game::with('competition')->get()->filter(function ($g) {
            return str_starts_with($g->competition->type ?? '', 'team');
        });

        // Prepare next available frame number per game (1..12). Use max game_no where possible.
        // If a game already has 12 frames, mark as null so the UI can indicate it's full.
        $gameNextNumbers = [];
        foreach ($games as $g) {
            $max = $g->frames()->max('game_no');
            $next = ($max ?: 0) + 1;
            $gameNextNumbers[$g->id] = $next > 12 ? null : $next;
        }

        // Preload players for home/away teams per game for client convenience.
        $homePlayersByGame = [];
        $awayPlayersByGame = [];
        foreach ($games as $g) {
            $homePlayersByGame[$g->id] = [];
            $awayPlayersByGame[$g->id] = [];
            if ($g->home_team_id) {
                $homeTeam = $g->homeTeam()->with(['players'])->first();
                if ($homeTeam) {
                    $homePlayersByGame[$g->id] = $homeTeam->players()->orderBy('name')->get()->map(function($p){ return ['id' => $p->id, 'name' => $p->name]; })->toArray();
                }
            }
            if ($g->away_team_id) {
                $awayTeam = $g->awayTeam()->with(['players'])->first();
                if ($awayTeam) {
                    $awayPlayersByGame[$g->id] = $awayTeam->players()->orderBy('name')->get()->map(function($p){ return ['id' => $p->id, 'name' => $p->name]; })->toArray();
                }
            }
            // Prepare empty player frame counts placeholder (we'll fill after loop)
            
        }

        // Count how many frames each player has played in each game (home or away)
        $playerFrameCountsByGame = [];
        foreach ($games as $g) {
            $counts = [];
            $frames = $g->frames()->get(['home_player', 'away_player']);
            foreach ($frames as $f) {
                if ($f->home_player) {
                    $counts[$f->home_player] = ($counts[$f->home_player] ?? 0) + 1;
                }
                if ($f->away_player) {
                    $counts[$f->away_player] = ($counts[$f->away_player] ?? 0) + 1;
                }
            }
            $playerFrameCountsByGame[$g->id] = $counts;
        }

        $players = Player::orderBy('name')->get();

    return view('frames.create', compact('games', 'players', 'gameNextNumbers', 'homePlayersByGame', 'awayPlayersByGame', 'playerFrameCountsByGame'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'game_id' => 'required|string|exists:games,id',
            'home_player' => 'nullable|string|exists:players,id',
            'away_player' => 'nullable|string|exists:players,id',
            'game_no' => 'nullable|integer|min:1|max:12',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'eight_ball_clear_home' => 'boolean',
            'eight_ball_clear_away' => 'boolean',
            'home_game_no' => 'nullable|integer|min:0',
            'away_game_no' => 'nullable|integer|min:0',
        ]);

        // Ensure the game is a team competition
        $game = Game::find($data['game_id']);
        if (! $game || ! str_starts_with($game->competition->type ?? '', 'team')) {
            return back()->withErrors(['game_id' => 'Selected game is not a team competition.'])->withInput();
        }

        // Auto-assign next frame/game_no if not provided. Return an error if the game already has 12 frames.
        if (empty($data['game_no'])) {
            $max = $game->frames()->max('game_no');
            $next = ($max ?: 0) + 1;
            if ($next > 12) {
                return back()->withErrors(['game_id' => 'Selected game already has the maximum of 12 frames.'])->withInput();
            }
            $data['game_no'] = $next;
        }

        $frame = Frame::create($data + [
            'eight_ball_clear_home' => $request->boolean('eight_ball_clear_home'),
            'eight_ball_clear_away' => $request->boolean('eight_ball_clear_away'),
        ]);

        return redirect()->route('frames.show', $frame)->with('success', 'Frame created');
    }

    public function show(Frame $frame)
    {
        $frame->load(['game.competition', 'homePlayer', 'awayPlayer']);
        return view('frames.show', compact('frame'));
    }

    public function edit(Frame $frame)
    {
        $games = Game::with('competition')->get()->filter(function ($g) {
            return str_starts_with($g->competition->type ?? '', 'team');
        });
        $players = Player::orderBy('name')->get();
        // also provide next numbers for client-side convenience if needed
        $gameNextNumbers = [];
        $homePlayersByGame = [];
        $awayPlayersByGame = [];
        foreach ($games as $g) {
            $max = $g->frames()->max('game_no');
            $next = ($max ?: 0) + 1;
            $gameNextNumbers[$g->id] = $next > 12 ? null : $next;

            $homePlayersByGame[$g->id] = [];
            $awayPlayersByGame[$g->id] = [];
            if ($g->home_team_id) {
                $homeTeam = $g->homeTeam()->with(['players'])->first();
                if ($homeTeam) {
                    $homePlayersByGame[$g->id] = $homeTeam->players()->orderBy('name')->get()->map(function($p){ return ['id' => $p->id, 'name' => $p->name]; })->toArray();
                }
            }
            if ($g->away_team_id) {
                $awayTeam = $g->awayTeam()->with(['players'])->first();
                if ($awayTeam) {
                    $awayPlayersByGame[$g->id] = $awayTeam->players()->orderBy('name')->get()->map(function($p){ return ['id' => $p->id, 'name' => $p->name]; })->toArray();
                }
            }
        }

        // Count how many frames each player has played in each game (home or away)
        $playerFrameCountsByGame = [];
        foreach ($games as $g) {
            $counts = [];
            $frames = $g->frames()->get(['home_player', 'away_player']);
            foreach ($frames as $f) {
                if ($f->home_player) {
                    $counts[$f->home_player] = ($counts[$f->home_player] ?? 0) + 1;
                }
                if ($f->away_player) {
                    $counts[$f->away_player] = ($counts[$f->away_player] ?? 0) + 1;
                }
            }
            $playerFrameCountsByGame[$g->id] = $counts;
        }

        return view('frames.edit', compact('frame', 'games', 'players', 'gameNextNumbers', 'homePlayersByGame', 'awayPlayersByGame', 'playerFrameCountsByGame'));
    }

    public function update(Request $request, Frame $frame)
    {
        $data = $request->validate([
            'game_id' => 'required|string|exists:games,id',
            'home_player' => 'nullable|string|exists:players,id',
            'away_player' => 'nullable|string|exists:players,id',
            'game_no' => 'nullable|integer|min:1|max:12',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'eight_ball_clear_home' => 'boolean',
            'eight_ball_clear_away' => 'boolean',
            'home_game_no' => 'nullable|integer|min:0',
            'away_game_no' => 'nullable|integer|min:0',
        ]);

        $game = Game::find($data['game_id']);
        if (! $game || ! str_starts_with($game->competition->type ?? '', 'team')) {
            return back()->withErrors(['game_id' => 'Selected game is not a team competition.'])->withInput();
        }

        $frame->update($data + [
            'eight_ball_clear_home' => $request->boolean('eight_ball_clear_home'),
            'eight_ball_clear_away' => $request->boolean('eight_ball_clear_away'),
        ]);

        return redirect()->route('frames.show', $frame)->with('success', 'Frame updated');
    }

    public function destroy(Frame $frame)
    {
        $frame->delete();
        return redirect()->route('frames.index')->with('success', 'Frame deleted');
    }
}
