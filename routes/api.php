<?php

use App\Http\Controllers\JokeController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

// Jokes API
Route::apiResource('jokes', JokeController::class)->only(['index', 'show']);

// Tracker API
Route::post('/track', [TrackController::class, 'store']);

Route::get('/track', function () {
    return response()->json([
        'status'  => 'ok',
        'message' => 'Tracker endpoint is active.',
        'time'    => now()->toDateTimeString(),
    ]);
});