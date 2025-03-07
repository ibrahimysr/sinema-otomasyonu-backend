<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    public function index()
    {
        $cinemas = Cinema::with('city')->get();

        return response()->json([
            'success' => true,
            'data' => $cinemas,
            'message' => 'Sinemalar başarıyla listelendi.',
        ], 200);
    }

    public function show($id)
    {
        try {
            $cinema = Cinema::with('city')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $cinema,
                'message' => 'Sinema başarıyla bulundu.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Sinema bulunamadı.',
            ], 404); 
        }
    }

    public function byCity($city_id)
    {
        $cinemas = Cinema::where('city_id', $city_id)->with('city')->get();

        if ($cinemas->isEmpty()) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Bu şehirde sinema bulunamadı.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cinemas,
            'message' => 'Şehre ait sinemalar başarıyla listelendi.',
        ], 200);
    }
}