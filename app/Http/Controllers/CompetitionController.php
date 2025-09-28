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

        return view('competitions.show', compact('competition'));
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
