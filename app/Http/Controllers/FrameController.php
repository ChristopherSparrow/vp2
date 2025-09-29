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

        $players = Player::orderBy('name')->get();

        return view('frames.create', compact('games', 'players'));
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
        return view('frames.edit', compact('frame', 'games', 'players'));
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
