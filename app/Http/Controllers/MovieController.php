<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Tüm filmleri listele
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Movie::where('is_in_theaters', true);
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $movies = $query->orderBy('release_date', 'desc')
            ->paginate(12)
            ->withPath(request()->url())
            ->appends(request()->query());
        
        return view('movies.index', compact('movies'));
    }
    
    /**
     * Film detayını göster
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $movie = Movie::findOrFail($id);
        $cities = City::orderBy('name')->get();
        
        return view('movies.show', compact('movie', 'cities'));
    }
} 