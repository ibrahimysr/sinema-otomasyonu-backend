<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::all();

        return response()->json([
            'success' => true,
            'data' => $cities,
            'message' => 'Şehirler başarıyla listelendi.',
        ], 200); 
    }

    public function show($id)
    {
        try {
            $city = City::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $city,
                'message' => 'Şehir başarıyla bulundu.',
            ], 200); 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Şehir bulunamadı.',
            ], 404); 
        }
    }
}