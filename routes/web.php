<?php

use App\Http\Controllers\GenerateMarsImageController;
use App\Http\Controllers\MarsGalleryController;
use App\Http\Controllers\MarsWeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarsWeatherController::class, 'index'])->name('mars.index');
Route::get('/statistics', [MarsWeatherController::class, 'getMonthStatistics'])->name('mars.statistics');
Route::post('/mars/generate-image', GenerateMarsImageController::class)->name('mars.generate-image');
Route::get('/gallery', [MarsGalleryController::class, 'index'])->name('mars.gallery');
