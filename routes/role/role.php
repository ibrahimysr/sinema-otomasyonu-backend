<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('role')->group(function () {
    Route::get('/roles', [RoleController::class, 'listRoles'])->middleware(['auth', 'check.role:admin,super_admin']);
    Route::post('/change-user-role', [RoleController::class, 'changeUserRole'])->middleware(['auth', 'check.role:admin,super_admin']);
    Route::get('/users/role/{roleId}', [RoleController::class, 'getUsersByRole'])->middleware(['auth', 'check.role:admin,super_admin']);
});