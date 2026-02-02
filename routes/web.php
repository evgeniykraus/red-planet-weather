<?php

use App\Http\Controllers\MarsWeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarsWeatherController::class, 'index'])->name('mars.index');
Route::get('/statistics', [MarsWeatherController::class, 'getMonthStatistics'])->name('mars.statistics');
