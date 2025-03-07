<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CinemaController;

Route::prefix('cinemas')->middleware('auth')->group(function () {
    Route::get('/', [CinemaController::class, 'index']); 
    Route::get('/{id}', [CinemaController::class, 'show']); 
    Route::get('/city/{city_id}', [CinemaController::class, 'byCity']); 
});