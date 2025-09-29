<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionStatsController extends Controller
{
    /**
     * Display basic stats for a competition.
     */
    public function stats(Competition $competition)
    {
        // For now return a simple view with the competition model.
        return view('competitions.stats', compact('competition'));
    }
}
