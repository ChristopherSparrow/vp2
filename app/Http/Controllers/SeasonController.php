<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SeasonController extends Controller
{
    /** Display a listing of the seasons. */
    public function index()
    {
        $seasons = Season::orderBy('start_date', 'desc')->paginate(15);
        return view('seasons.index', compact('seasons'));
    }

    /** Show the form for creating a new season. */
    public function create()
    {
        return view('seasons.create');
    }

    /** Store a newly created season in storage. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:seasons,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'current' => 'sometimes|boolean',
        ]);

        // generate ULID if model expects non-incrementing string id
        // ensure boolean value is present (checkboxes submit 'on' or null)
        $data['current'] = !empty($data['current']) ? true : false;

        // Use transaction: if setting current, clear others first
        DB::transaction(function () use ($data, &$season) {
            if (!empty($data['current'])) {
                Season::where('current', true)->update(['current' => false]);
            }

            $season = new Season($data);
            if (empty($season->getKey())) {
                $season->{$season->getKeyName()} = (string) Str::ulid();
            }
            $season->save();
        });

        return redirect()->route('seasons.index')->with('success', 'Season created.');
    }

    /** Display the specified season. */
    public function show(Season $season)
    {
        return view('seasons.show', compact('season'));
    }

    /** Show the form for editing the specified season. */
    public function edit(Season $season)
    {
        return view('seasons.edit', compact('season'));
    }

    /** Update the specified season in storage. */
    public function update(Request $request, Season $season)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:seasons,name,' . $season->getKey(),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'current' => 'sometimes|boolean',
        ]);

        $data['current'] = !empty($data['current']) ? true : false;

        DB::transaction(function () use ($data, $season) {
            if (!empty($data['current'])) {
                Season::where('current', true)->where('id', '!=', $season->getKey())->update(['current' => false]);
            }

            $season->update($data);
        });

        return redirect()->route('seasons.index')->with('success', 'Season updated.');
    }

    /** Remove the specified season from storage. */
    public function destroy(Season $season)
    {
        $season->delete();
        return redirect()->route('seasons.index')->with('success', 'Season deleted.');
    }
}
