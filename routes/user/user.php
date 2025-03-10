<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::prefix('users')->group(function () {
    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::get('/user-list', [UserController::class, 'index'])->name('user-list');
    });
}); 