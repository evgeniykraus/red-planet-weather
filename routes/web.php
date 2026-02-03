<?php

use App\Http\Controllers\AresArchiveController;
use App\Http\Controllers\GenerateMarsImageController;
use App\Http\Controllers\MarsGalleryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AresArchiveController::class, 'index'])->name('ares.archive');
Route::get('/statistics', [AresArchiveController::class, 'getMonthStatistics'])->name('ares.statistics');
Route::post('/mars/generate-image', GenerateMarsImageController::class)->name('mars.generate-image');
Route::get('/gallery', [MarsGalleryController::class, 'index'])->name('mars.gallery');
