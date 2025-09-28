<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('seasons', SeasonController::class);
Route::resource('teams', TeamController::class);
Route::resource('players', PlayerController::class);
Route::resource('competitions', \App\Http\Controllers\CompetitionController::class);
Route::resource('games', \App\Http\Controllers\GameController::class);

// Authentication routes
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register.post');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
