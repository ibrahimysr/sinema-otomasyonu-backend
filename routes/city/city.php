<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController; 


Route::prefix('cities')->middleware('auth')->group(function () {
    Route::get('/', [CityController::class, 'index']); 
    Route::get('/{id}', [CityController::class, 'show']); 
});

