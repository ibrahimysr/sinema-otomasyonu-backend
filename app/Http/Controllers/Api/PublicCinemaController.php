<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicCinemaController extends Controller
{
    /**
     * Tüm sinemaları listele
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $cinemas = Cinema::with('city')
            ->orderBy('name')
            ->paginate(12);
        
        return view('cinemas.index', compact('cinemas'));
    }
    
    /**
     * Sinema detayını göster
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $cinema = Cinema::with(['halls', 'city'])
            ->findOrFail($id);
        
        $showtimes = \App\Models\Showtime::with(['movie', 'cinemaHall'])
            ->whereHas('cinemaHall', function($query) use ($id) {
                $query->where('cinema_id', $id);
            })
            ->where('start_time', '>=', now())
            ->where('available_seats', '>', 0)
            ->orderBy('start_time')
            ->take(10)
            ->get();
        
        return view('cinemas.show', compact('cinema', 'showtimes'));
    }

    /**
     * Şehre göre sinemaları getir
     *
     * @param int $cityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCinemasByCity($cityId): JsonResponse
    {
        $cinemas = Cinema::with('city')
            ->where('city_id', $cityId)
            ->get();

        return response()->json($cinemas);
    }
} 