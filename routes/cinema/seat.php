<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SeatController;

Route::prefix('seats')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/seat-list', [SeatController::class, 'index'])->name('seat-list');
        Route::get('/seat-detail/{id}', [SeatController::class, 'show'])->name('seat-detail');
        Route::get('/seat-by-hall/{hall_id}', [SeatController::class, 'byHall'])->name('seat-by-hall');
    });

    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::post('/seat-add', [SeatController::class, 'store'])->name('seat-add');
        Route::post('/seat-update/{id}', [SeatController::class, 'update'])->name('seat-update');
        Route::post('/seat-delete/{id}', [SeatController::class, 'destroy'])->name('seat-delete');
    });
}); 