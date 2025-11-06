<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    /** Display a listing of the players. */
    public function index()
    {
        $players = Player::with(['playerTeams.team'])->orderBy('name')->paginate(15);
        return view('players.index', compact('players'));
    }

    /** Show the form for creating a new player. */
    public function create()
    {
        // prefer teams for the current season, fallback to all teams
        $season = \App\Models\Season::where('current', true)->first();
        $teams = $season ? $season->teams()->orderBy('name')->get() : Team::orderBy('name')->get();
        return view('players.create', compact('teams'));
    }

    /** Store a newly created player in storage. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:32',
            'team_ids' => 'nullable|array',
            'team_ids.*' => 'nullable|string|exists:teams,id',
        ]);

        $teamIds = $request->input('team_ids', []);

        DB::transaction(function () use ($data, $teamIds, &$player) {
            $player = new Player($data);
            if (empty($player->getKey())) {
                $player->{$player->getKeyName()} = (string) Str::ulid();
            }
            $player->save();

            // persist team assignments (multi-select): create PlayerTeam rows for each selected team
            foreach ((array) $teamIds as $tid) {
                if (empty($tid)) continue;
                $team = \App\Models\Team::find($tid);
                $seasonStart = $team && $team->season && $team->season->start_date ? $team->season->start_date : now()->toDateString();
                $pt = new \App\Models\PlayerTeam([
                    'player_id' => $player->id,
                    'team_id' => $tid,
                    'start_date' => $seasonStart ?? now()->toDateString(),
                    'end_date' => null,
                ]);
                $pt->{$pt->getKeyName()} = (string) Str::ulid();
                $pt->save();
            }
        });

        return redirect()->route('players.index')->with('success', 'Player created.');
    }

    /** Display the specified player. */
    public function show(Player $player)
    {
        return view('players.show', compact('player'));
    }

    /** Show the form for editing the specified player. */
    public function edit(Player $player)
    {
        $season = \App\Models\Season::where('current', true)->first();
        $teams = $season ? $season->teams()->orderBy('name')->get() : Team::orderBy('name')->get();
        return view('players.edit', compact('player','teams'));
    }

    /** Update the specified player in storage. */
    public function update(Request $request, Player $player)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:32',
        ]);

        $teamIds = $request->input('team_ids', []);

        DB::transaction(function () use ($player, $data, $teamIds) {
            $player->update($data);

            // Simple sync: remove all existing PlayerTeam rows for this player and re-create from submitted team ids
            \App\Models\PlayerTeam::where('player_id', $player->id)->delete();

            foreach ((array) $teamIds as $tid) {
                if (empty($tid)) continue;
                $team = \App\Models\Team::find($tid);
                $start = $team && $team->season && $team->season->start_date ? $team->season->start_date : now()->toDateString();
                $pt = new \App\Models\PlayerTeam([
                    'player_id' => $player->id,
                    'team_id' => $tid,
                    'start_date' => $start,
                    'end_date' => null,
                ]);
                $pt->{$pt->getKeyName()} = (string) Str::ulid();
                $pt->save();
            }
        });

        return redirect()->route('players.index')->with('success', 'Player updated.');
    }

    /** Remove the specified player from storage. */
    public function destroy(Player $player)
    {
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted.');
    }
}
