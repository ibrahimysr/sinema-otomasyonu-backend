<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth/auth.php'; 
require __DIR__ . '/role/role.php';
require __DIR__ . '/city/city.php';
require __DIR__ . '/cinema/cinema.php';
require __DIR__ . '/cinema/cinema_hall.php';
require __DIR__ . '/cinema/seat.php';
require __DIR__ . '/movie/movie.php';
require __DIR__ . '/cinema/showtime.php';
require __DIR__ . '/ticket/ticket.php';
require __DIR__ . '/payment/payment.php';
require __DIR__ . '/user/user.php';

// Dashboard API rotaları
Route::prefix('dashboard')->middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
    Route::get('/statistics', [\App\Http\Controllers\Api\DashboardController::class, 'getStatistics']);
    Route::get('/recent-tickets', [\App\Http\Controllers\Api\DashboardController::class, 'getRecentTickets']);
    Route::get('/today-showtimes', [\App\Http\Controllers\Api\DashboardController::class, 'getTodayShowtimes']);
    Route::get('/popular-movies', [\App\Http\Controllers\Api\DashboardController::class, 'getPopularMovies']);
    Route::get('/ticket-sales', [\App\Http\Controllers\Api\DashboardController::class, 'getTicketSales']);
});


