<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShowtimeController;

Route::prefix('showtimes')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/showtime-list', [ShowtimeController::class, 'index'])->name('showtime-list');
        Route::get('/showtime-detail/{id}', [ShowtimeController::class, 'show'])->name('showtime-detail');
        Route::get('/showtime-by-movie/{movie_id}', [ShowtimeController::class, 'byMovie'])->name('showtime-by-movie');
        Route::get('/showtime-by-hall/{cinema_hall_id}', [ShowtimeController::class, 'byCinemaHall'])->name('showtime-by-hall');
        Route::get('/datatable', [ShowtimeController::class, 'getShowtimes'])->name('showtime-datatable');
    });

    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::post('/showtime-add', [ShowtimeController::class, 'store'])->name('showtime-add');
        Route::post('/showtime-update/{id}', [ShowtimeController::class, 'update'])->name('showtime-update');
        Route::post('/showtime-delete/{id}', [ShowtimeController::class, 'destroy'])->name('showtime-delete');
    });
}); 