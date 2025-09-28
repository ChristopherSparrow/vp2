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
        $teams = Team::orderBy('name')->get();
        return view('players.create', compact('teams'));
    }

    /** Store a newly created player in storage. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'number' => 'nullable|integer|min:0',
        ]);

        $assignments = $request->input('assignments', []);

        DB::transaction(function () use ($data, $assignments, &$player) {
            $player = new Player($data);
            if (empty($player->getKey())) {
                $player->{$player->getKeyName()} = (string) Str::ulid();
            }
            $player->save();

            // persist assignments if any
            foreach($assignments as $a){
                if(empty($a['team_id']) || empty($a['start_date'])) continue;
                $pt = new \App\Models\PlayerTeam([
                    'player_id' => $player->id,
                    'team_id' => $a['team_id'],
                    'start_date' => $a['start_date'],
                    'end_date' => $a['end_date'] ?? null,
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
        $teams = Team::orderBy('name')->get();
        return view('players.edit', compact('player','teams'));
    }

    /** Update the specified player in storage. */
    public function update(Request $request, Player $player)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'number' => 'nullable|integer|min:0',
        ]);

        $assignments = $request->input('assignments', []);

        DB::transaction(function () use ($player, $data, $assignments) {
            $player->update($data);

            // Sync assignments: a simple approach — remove all existing PlayerTeam rows and re-create from input
            \App\Models\PlayerTeam::where('player_id', $player->id)->delete();

            foreach($assignments as $a){
                if(empty($a['team_id']) || empty($a['start_date'])) continue;
                $pt = new \App\Models\PlayerTeam([
                    'player_id' => $player->id,
                    'team_id' => $a['team_id'],
                    'start_date' => $a['start_date'],
                    'end_date' => $a['end_date'] ?? null,
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
