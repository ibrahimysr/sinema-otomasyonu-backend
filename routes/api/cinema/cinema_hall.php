<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CinemaHallController;

Route::prefix('cinema-halls')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/hall-list', [CinemaHallController::class, 'index'])->name('hall-list');
        Route::get('/hall-detail/{id}', [CinemaHallController::class, 'show'])->name('hall-detail');
        Route::get('/hall-by-cinema/{cinema_id}', [CinemaHallController::class, 'byCinema'])->name('hall-by-cinema');
        Route::get('/datatable', [CinemaHallController::class, 'getHalls'])->name('hall-datatable');
    });

    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::post('/hall-add', [CinemaHallController::class, 'store'])->name('hall-add');
        Route::post('/hall-update/{id}', [CinemaHallController::class, 'update'])->name('hall-update');
        Route::post('/hall-delete/{id}', [CinemaHallController::class, 'destroy'])->name('hall-delete');
    });
}); 