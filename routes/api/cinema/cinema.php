<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CinemaController;
Route::get('/cinemas/datatable', [CinemaController::class, 'getCinemas'])->middleware('auth');
Route::prefix('cinemas')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/cinema-list', [CinemaController::class, 'index'])->name('cinema-list'); 
        Route::get('/cinema-detail/{id}', [CinemaController::class, 'show'])->name('cinema-detail');
        Route::get('/cinema-by-city/{city_id}', [CinemaController::class, 'byCity'])->name('cinema-by-city'); 
    });

    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::post('/cinema-add', [CinemaController::class, 'store'])->name('cinema-add'); 
        Route::post('/cinema-update/{id}', [CinemaController::class, 'update'])->name('cinema-update'); 
        Route::post('/cinema-delete/{id}', [CinemaController::class, 'destroy'])->name('cinema-delete'); 
    });
});

