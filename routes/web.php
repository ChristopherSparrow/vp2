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

    // Compute a simple aggregated league table for the current season.
    $season = App\Models\Season::where('current', true)->first() ?? App\Models\Season::orderBy('start_date', 'desc')->first();
    $standings = null;

    if ($season) {
        // Consider team_league competitions for the season when building the table
        $competitionIds = $season->competitions()->where('type', 'team_league')->pluck('id')->all();

        $games = App\Models\Game::with(['homeTeam', 'awayTeam', 'competition'])
            ->whereIn('competition_id', $competitionIds)
            ->get();

        $table = [];

        $ensureTeam = function ($team) use (&$table) {
            if (!$team) return;
            $id = $team->getKey();
            if (!isset($table[$id])) {
                $table[$id] = [
                    'team_id' => $id,
                    'name' => $team->name,
                    'played' => 0,
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'for' => 0,
                ];
            }
        };

        foreach ($games as $game) {
            // Only team-vs-team games
            if (!$game->homeTeam || !$game->awayTeam) continue;

            // Skip incomplete games without scores
            if ($game->home_score === null || $game->away_score === null) continue;

            $ensureTeam($game->homeTeam);
            $ensureTeam($game->awayTeam);

            $homeId = $game->homeTeam->getKey();
            $awayId = $game->awayTeam->getKey();

            $table[$homeId]['played']++;
            $table[$awayId]['played']++;

            $table[$homeId]['for'] += $game->home_score;
            $table[$awayId]['for'] += $game->away_score;

            if ($game->home_score > $game->away_score) {
                $table[$homeId]['wins']++;
                $table[$awayId]['losses']++;
            } elseif ($game->home_score < $game->away_score) {
                $table[$awayId]['wins']++;
                $table[$homeId]['losses']++;
            } else {
                $table[$homeId]['draws']++;
                $table[$awayId]['draws']++;
            }
        }

        // Convert to list, compute points and sort.
        $standings = array_values($table);
        foreach ($standings as &$row) {
            $row['points'] = ($row['wins'] * 3) + ($row['draws'] * 1);
            $row['goal_diff'] = $row['for'];
        }
        unset($row);

        usort($standings, function ($a, $b) {
            if ($a['points'] !== $b['points']) return $b['points'] <=> $a['points'];
            if ($a['goal_diff'] !== $b['goal_diff']) return $b['goal_diff'] <=> $a['goal_diff'];
            if ($a['wins'] !== $b['wins']) return $b['wins'] <=> $a['wins'];
            return $a['played'] <=> $b['played'];
        });
    }

    return view('welcome', compact('fixtures', 'standings', 'season'));
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
