<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportMoviesRequest;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Services\MovieService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Movie; // Movie modelinizi dahil edin

class MovieController extends Controller
{
    protected $movieService;
    protected $responseService;

    /**
     * MovieController constructor.
     *
     * @param MovieService $movieService
     * @param ResponseService $responseService
     */
    public function __construct(MovieService $movieService, ResponseService $responseService)
    {
        $this->movieService = $movieService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm filmleri listele
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $movies = $this->movieService->getPaginatedMovies($perPage);
        
        return $this->responseService->success(
            $movies,
            'Filmler başarıyla listelendi.'
        );
    }

    /**
     * Belirli bir filmi göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $movie = $this->movieService->getMovieById($id);
        
        if (!$movie) {
            return $this->responseService->notFound('Film bulunamadı.');
        }
        
        return $this->responseService->success($movie, 'Film başarıyla bulundu.');
    }

    /**
     * Başlığa göre film ara
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $title = $request->input('title');
        
        if (!$title) {
            return $this->responseService->error('Film başlığı belirtmelisiniz.', null, 422);
        }
        
        $movies = $this->movieService->searchMoviesByTitle($title);
        
        if ($movies->isEmpty()) {
            return $this->responseService->notFound('Film bulunamadı.');
        }
        
        return $this->responseService->success($movies, 'Filmler başarıyla listelendi.');
    }

    /**
     * Yeni bir film oluştur
     *
     * @param StoreMovieRequest $request
     * @return JsonResponse
     */
    public function store(StoreMovieRequest $request): JsonResponse
    {
        $movie = $this->movieService->createMovie($request->validated());
        return $this->responseService->success($movie, 'Film başarıyla eklendi.', 201);
    }

    /**
     * Mevcut bir filmi güncelle
     *
     * @param UpdateMovieRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateMovieRequest $request, int $id): JsonResponse
    {
        $movie = $this->movieService->updateMovie($id, $request->validated());
        
        if (!$movie) {
            return $this->responseService->notFound('Film bulunamadı.');
        }
        
        return $this->responseService->success($movie, 'Film başarıyla güncellendi.');
    }

    /**
     * Bir filmi sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->movieService->deleteMovie($id);
        
        if (!$result) {
            return $this->responseService->notFound('Film bulunamadı.');
        }
        
        return $this->responseService->success(null, 'Film başarıyla silindi.');
    }

    /**
     * Tüm filmleri sayfalama olmadan getir (dropdown için)
     *
     * @return JsonResponse
     */
    public function getAllMovies(): JsonResponse
    {
        $movies = $this->movieService->getAllMovies();
        return $this->responseService->success($movies, 'Filmler başarıyla listelendi.');
    }

    /**
     * OMDB API'den film ara ve veritabanına ekle
     *
     * @param ImportMoviesRequest $request
     * @return JsonResponse
     */
    public function import(ImportMoviesRequest $request): JsonResponse
    {
        if ($request->has('title')) {
            $title = $request->input('title');
            $page = $request->input('page', 1);
            
            $result = $this->movieService->searchAndImportMovies($title, $page);
            
            if (!$result['success']) {
                return $this->responseService->error($result['message'] ?? 'Film bulunamadı.', null, 404);
            }
            
            return $this->responseService->success(
                $result['data'],
                sprintf('%d film başarıyla içe aktarıldı.', $result['imported'])
            );
        } else {
            $count = $request->input('count', 20);
            
            $result = $this->movieService->importPopularMovies($count);
            
            if (!$result['success']) {
                return $this->responseService->error($result['message'] ?? 'Filmler içe aktarılamadı.', null, 500);
            }
            
            return $this->responseService->success(
                null,
                sprintf('%d popüler film başarıyla içe aktarıldı.', $result['imported'])
            );
        }
    }

    public function getMovies(Request $request)
{
    $query = Movie::query();

    // Arama filtreleri
    if ($request->has('title') && $request->title) {
        $query->where('title', 'like', '%' . $request->title . '%');
    }
    if ($request->has('genre') && $request->genre) {
        $query->where('genre', 'like', '%' . $request->genre . '%');
    }
    if ($request->has('release_year') && $request->release_year) {
        $query->whereYear('release_date', $request->release_year);
    }

    return DataTables::of($query)
        ->addColumn('poster', function ($movie) {
            return $movie->poster_url 
                ? '<img src="' . $movie->poster_url . '" width="50" height="75" alt="' . $movie->title . '" class="img-thumbnail hover-zoom">'
                : '<div class="bg-light text-center" style="width:50px;height:75px;"><i class="fas fa-film mt-4"></i></div>';
        })
        ->addColumn('release_date', function ($movie) {
            return $movie->release_date ? $movie->release_date->format('d.m.Y') : 'Belirtilmemiş';
        })
        ->addColumn('imdb_rating', function ($movie) {
            return $movie->imdb_rating 
                ? '<span class="badge bg-warning text-dark">' . $movie->imdb_rating . '</span>' 
                : '-';
        })
        ->addColumn('is_in_theaters', function ($movie) {
            return $movie->is_in_theaters 
                ? '<span class="badge bg-gradient-success">Gösterimde</span>' 
                : '<span class="badge bg-gradient-danger">Gösterimde Değil</span>';
        })
        ->addColumn('actions', function ($movie) {
            return '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-info edit-movie" data-id="' . $movie->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-movie" data-id="' . $movie->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            ';
        })
        ->rawColumns(['poster', 'imdb_rating', 'is_in_theaters', 'actions']) // HTML içeriği için
        ->make(true);
}
}