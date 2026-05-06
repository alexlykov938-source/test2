<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

// Авторизация
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Статистика — только для авторизованных
Route::middleware('auth')->group(function () {
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
});

Route::redirect('/', '/stats');