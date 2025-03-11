<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\PublicCinemaController;
use App\Http\Controllers\Api\PublicShowtimeController;
use Illuminate\Http\Request;

require __DIR__ . '/api/auth/auth.php'; 
require __DIR__ . '/api/role/role.php';
require __DIR__ . '/api/city/city.php';
require __DIR__ . '/api/cinema/cinema.php';
require __DIR__ . '/api/cinema/cinema_hall.php';
require __DIR__ . '/api/cinema/seat.php';
require __DIR__ . '/api/movie/movie.php';
require __DIR__ . '/api/cinema/showtime.php';
require __DIR__ . '/api/ticket/ticket.php';
require __DIR__ . '/api/payment/payment.php';
require __DIR__ . '/api/user/user.php';

Route::get('/cities/{cityId}/cinemas', [PublicCinemaController::class, 'getCinemasByCity']);
Route::get('/showtimes', [PublicShowtimeController::class, 'getShowtimes']);

Route::prefix('dashboard')->middleware(['auth', 'check.role:admin,super_admin'])->group(function () {
    Route::get('/statistics', [\App\Http\Controllers\Api\DashboardController::class, 'getStatistics']);
    Route::get('/recent-tickets', [\App\Http\Controllers\Api\DashboardController::class, 'getRecentTickets']);
    Route::get('/today-showtimes', [\App\Http\Controllers\Api\DashboardController::class, 'getTodayShowtimes']);
    Route::get('/popular-movies', [\App\Http\Controllers\Api\DashboardController::class, 'getPopularMovies']);
    Route::get('/ticket-sales', [\App\Http\Controllers\Api\DashboardController::class, 'getTicketSales']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

