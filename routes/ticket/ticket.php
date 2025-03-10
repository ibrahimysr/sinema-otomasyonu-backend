<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;

Route::prefix('tickets')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/ticket-list', [TicketController::class, 'index'])->name('ticket-list');
        Route::get('/ticket-detail/{id}', [TicketController::class, 'show'])->name('ticket-detail');
        Route::get('/ticket-by-code/{code}', [TicketController::class, 'byCode'])->name('ticket-by-code');
        Route::get('/ticket-by-user/{user_id}', [TicketController::class, 'byUser'])->name('ticket-by-user');
        Route::get('/ticket-by-showtime/{showtime_id}', [TicketController::class, 'byShowtime'])->name('ticket-by-showtime');
        Route::get('/datatable', [TicketController::class, 'getTickets'])->name('ticket-datatable');
        
        // Bilet oluşturma (kullanıcılar da bilet alabilir)
        Route::post('/ticket-add', [TicketController::class, 'store'])->name('ticket-add');
    });

    Route::middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
        Route::post('/ticket-update/{id}', [TicketController::class, 'update'])->name('ticket-update');
        Route::post('/ticket-delete/{id}', [TicketController::class, 'destroy'])->name('ticket-delete');
    });
}); 