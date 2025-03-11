<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicCinemaController extends Controller
{
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