<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PublicShowtimeController extends Controller
{
    /**
     * Film, sinema ve tarihe göre seansları getir
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShowtimes(Request $request): JsonResponse
    {
        $movieId = $request->input('movie_id');
        $cinemaId = $request->input('cinema_id');
        $date = $request->input('date');
        
        // Hata ayıklama için parametreleri logla
        Log::info('Seans sorgusu parametreleri:', [
            'movie_id' => $movieId,
            'cinema_id' => $cinemaId,
            'date' => $date
        ]);
        
        $query = Showtime::with(['cinemaHall', 'cinemaHall.cinema', 'movie']);
        
        // Film ID'si varsa filtre uygula
        if ($movieId) {
            $query->where('movie_id', $movieId);
        }
        
        // Sinema ID'si varsa filtre uygula
        if ($cinemaId) {
            $query->whereHas('cinemaHall', function($q) use ($cinemaId) {
                $q->where('cinema_id', $cinemaId);
            });
        }
        
        // Tarih varsa filtre uygula
        if ($date) {
            $startOfDay = Carbon::parse($date)->startOfDay();
            $endOfDay = Carbon::parse($date)->endOfDay();
            
            $query->whereBetween('start_time', [$startOfDay, $endOfDay]);
        }
        
        $showtimes = $query->orderBy('start_time')->get();
        
        // Hata ayıklama için sonuçları logla
        Log::info('Seans sorgusu sonuçları:', [
            'count' => $showtimes->count(),
            'first_showtime' => $showtimes->first()
        ]);
        
        return response()->json($showtimes);
    }
} 