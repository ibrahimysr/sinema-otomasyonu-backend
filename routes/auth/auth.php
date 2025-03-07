<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth');
    Route::get('/user', [AuthController::class, 'userProfile'])->middleware('auth');

    Route::get('/roles', [RoleController::class, 'listRoles'])->middleware(['auth', 'check.role:admin,super_admin']);
    Route::post('/change-user-role', [RoleController::class, 'changeUserRole'])->middleware(['auth', 'check.role:admin,super_admin']);
    Route::get('/users/role/{roleId}', [RoleController::class, 'getUsersByRole'])->middleware(['auth', 'check.role:admin,super_admin']);
});