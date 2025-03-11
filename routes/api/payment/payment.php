<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::prefix('payments')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/payment-list', [PaymentController::class, 'index'])->name('payment-list');
        Route::get('/payment-detail/{id}', [PaymentController::class, 'show'])->name('payment-detail');
        Route::get('/payment-by-user/{user_id}', [PaymentController::class, 'byUser'])->name('payment-by-user');
        Route::get('/payment-by-ticket/{ticket_id}', [PaymentController::class, 'byTicket'])->name('payment-by-ticket');
        Route::get('/payment-by-status/{status}', [PaymentController::class, 'byStatus'])->name('payment-by-status');
        
        // Ödeme oluşturma (kullanıcılar da ödeme yapabilir)
        Route::post('/payment-add', [PaymentController::class, 'store'])->name('payment-add');
    });

    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::get('/datatable', [PaymentController::class, 'datatable'])->name('payment-datatable');
        Route::get('/stats', [PaymentController::class, 'getStats'])->name('payment-stats');
        Route::post('/payment-update/{id}', [PaymentController::class, 'update'])->name('payment-update');
        Route::post('/payment-delete/{id}', [PaymentController::class, 'destroy'])->name('payment-delete');
    });
}); 