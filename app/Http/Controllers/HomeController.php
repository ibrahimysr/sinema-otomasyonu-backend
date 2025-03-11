<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Ana sayfayı göster
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $movies = Movie::where('is_in_theaters', true)
            ->orderBy('release_date', 'desc')
            ->take(8)
            ->get();
        
        $cinemas = Cinema::inRandomOrder()
            ->take(3)
            ->get();
        
        $showtimes = Showtime::with(['movie', 'cinemaHall.cinema'])
            ->where('start_time', '>=', Carbon::now())
            ->where('start_time', '<=', Carbon::now()->addDays(2))
            ->where('available_seats', '>', 0)
            ->orderBy('start_time')
            ->take(5)
            ->get();
        
        return view('home', compact('movies', 'cinemas', 'showtimes'));
    }
} 