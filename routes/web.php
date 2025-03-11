<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Api\PublicCinemaController;
use App\Http\Controllers\UserAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/showtimes', [ShowtimeController::class, 'index'])->name('showtimes.index');
Route::get('/showtimes/{id}', [ShowtimeController::class, 'show'])->name('showtimes.show');
Route::post('/showtimes/select-seats', [ShowtimeController::class, 'selectSeats'])->name('showtimes.select-seats');

Route::get('/cinemas', [PublicCinemaController::class, 'index'])->name('cinemas.index');
Route::get('/cinemas/{id}', [PublicCinemaController::class, 'show'])->name('cinemas.show');
Route::get('/cinemas/by-city/{cityId}', [PublicCinemaController::class, 'getCinemasByCity'])->name('cinemas.by-city');

Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/confirmation', [TicketController::class, 'confirmation'])->name('tickets.confirmation');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');
    
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('admin.users');
    
    Route::get('/movies', function () {
        return view('admin.movies.index');
    })->name('admin.movies');
    
    
    Route::get('/cinemas', function () {
        return view('admin.cinemas.index');
    })->name('admin.cinemas');
    
    Route::get('/cinema-halls', function () {
        return view('admin.cinema-halls.index');
    })->name('admin.cinema-halls');
    
    Route::get('/showtimes', function () {
        return view('admin.showtimes.index');
    })->name('admin.showtimes');
    
    Route::get('/tickets', function () {
        return view('admin.tickets.index');
    })->name('admin.tickets');
    
    Route::get('/payments', function () {
        return view('admin.payments.index');
    })->name('admin.payments');
});

Route::get('/login', function () {
    return view('admin.auth.login');
})->name('login');

Route::get('/logout', function () {
    return view('admin.auth.logout');
})->name('logout');

Route::get('/login-user', [UserAuthController::class, 'showLoginForm'])->name('login-user');
Route::post('/login-user', [UserAuthController::class, 'login']);
Route::post('/logout-user', [UserAuthController::class, 'logout'])->name('logout-user');

Route::get('/register-user', [UserAuthController::class, 'showRegisterForm'])->name('register-user');
Route::post('/register-user', [UserAuthController::class, 'register']);
