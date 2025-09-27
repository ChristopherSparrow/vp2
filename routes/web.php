<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeasonController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('seasons', SeasonController::class);
