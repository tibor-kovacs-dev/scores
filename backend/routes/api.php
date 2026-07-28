<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StandingController;
use App\Http\Resources\TeamResource;
use App\Models\Team;

Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{id}', [GameController::class, 'show']);

Route::get('/teams/{id}', function ($id) {
    $team = Team::find($id);
    if (!$team) {
        return response()->json(['message' => 'Team not found'], 404);
    }
    return new TeamResource($team);
});

Route::get('/search', [SearchController::class, 'search']);

Route::get('/standings', [StandingController::class, 'index']);
Route::get('/top-scorers', [StandingController::class, 'topScorers']);