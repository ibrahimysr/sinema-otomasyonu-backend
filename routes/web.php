<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

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

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes - Middleware'i kaldırıyoruz çünkü client-side token kontrolü yapacağız
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');
    
    // Kullanıcılar
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('admin.users');
    
    // Filmler
    Route::get('/movies', function () {
        return view('admin.movies.index');
    })->name('admin.movies');
    
    // Sinemalar
    Route::get('/cinemas', function () {
        return view('admin.cinemas.index');
    })->name('admin.cinemas');
    
    // Sinema Salonları
    Route::get('/cinema-halls', function () {
        return view('admin.cinema-halls.index');
    })->name('admin.cinema-halls');
    
    // Seanslar
    Route::get('/showtimes', function () {
        return view('admin.showtimes.index');
    })->name('admin.showtimes');
    
    // Biletler
    Route::get('/tickets', function () {
        return view('admin.tickets.index');
    })->name('admin.tickets');
    
    // Ödemeler
    Route::get('/payments', function () {
        return view('admin.payments.index');
    })->name('admin.payments');
});

// Auth Routes
Route::get('/login', function () {
    return view('admin.auth.login');
})->name('login');

Route::get('/logout', function () {
    return view('admin.auth.logout');
})->name('logout');
