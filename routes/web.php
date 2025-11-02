<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompetitionStatsController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;

Route::get('/', function () {
    // Show fixtures: played in the last 5 days and upcoming in the next 5 days
    $now = now();
    $playedStart = $now->copy()->subDays(7)->startOfDay();
    $playedEnd = $now->copy()->endOfDay();

    $upcomingStart = $now->copy()->startOfDay();
    $upcomingEnd = $now->copy()->addDays(7)->endOfDay();

    $played = App\Models\Game::with(['competition', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
        ->whereBetween('date', [$playedStart, $playedEnd])
        ->get()
        ->map(function ($g) {
            $g->period = 'played';
            return $g;
        });

    $upcoming = App\Models\Game::with(['competition', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
        ->whereBetween('date', [$upcomingStart, $upcomingEnd])
        ->get()
        ->map(function ($g) {
            $g->period = 'upcoming';
            return $g;
        });

    // Merge and sort by date (asc). We'll group in the view by date then competition.
    $fixtures = $played->merge($upcoming)->sortBy('date')->values();

    return view('welcome', compact('fixtures'));
});

// Competition stats route
Route::get('/competitions/{competition}/stats', [CompetitionStatsController::class, 'stats'])->name('competitions.stats');

Route::resource('seasons', SeasonController::class);
Route::resource('teams', TeamController::class);
Route::resource('players', PlayerController::class);
Route::resource('competitions', \App\Http\Controllers\CompetitionController::class);
Route::resource('games', \App\Http\Controllers\GameController::class);
Route::resource('frames', \App\Http\Controllers\FrameController::class);

// Authentication routes
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register.post');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
