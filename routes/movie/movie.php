<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;



Route::get('/movies/datatable', [MovieController::class, 'getMovies'])->middleware('auth');
Route::prefix('movies')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/movie-list', [MovieController::class, 'index'])->name('movie-list');
        Route::get('/all-movies', [MovieController::class, 'getAllMovies'])->name('all-movies');
        Route::get('/movie-detail/{id}', [MovieController::class, 'show'])->name('movie-detail');
        Route::get('/movie-search', [MovieController::class, 'search'])->name('movie-search');
    });

    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::post('/movie-add', [MovieController::class, 'store'])->name('movie-add');
        Route::post('/movie-update/{id}', [MovieController::class, 'update'])->name('movie-update');
        Route::post('/movie-delete/{id}', [MovieController::class, 'destroy'])->name('movie-delete');
        Route::post('/movie-import', [MovieController::class, 'import'])->name('movie-import');
    });
}); 