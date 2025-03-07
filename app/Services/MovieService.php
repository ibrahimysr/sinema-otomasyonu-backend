<?php

namespace App\Services;

use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieService
{
    protected $movieRepository;
    protected $omdbApiService;

    public function __construct(MovieRepository $movieRepository, OmdbApiService $omdbApiService)
    {
        $this->movieRepository = $movieRepository;
        $this->omdbApiService = $omdbApiService;
    }

    /**
     * Tüm filmleri getir
     *
     * @return Collection
     */
    public function getAllMovies(): Collection
    {
        return $this->movieRepository->getAll();
    }

    /**
     * Filmleri sayfalı olarak getir
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedMovies(int $perPage = 15): LengthAwarePaginator
    {
        return $this->movieRepository->getPaginated($perPage);
    }

    /**
     * ID'ye göre film bul
     *
     * @param int $id
     * @return Movie|null
     */
    public function getMovieById(int $id): ?Movie
    {
        return $this->movieRepository->findById($id);
    }

    /**
     * Başlığa göre film ara
     *
     * @param string $title
     * @return Collection
     */
    public function searchMoviesByTitle(string $title): Collection
    {
        return $this->movieRepository->searchByTitle($title);
    }

    /**
     * Yeni film oluştur
     *
     * @param array $data
     * @return Movie
     */
    public function createMovie(array $data): Movie
    {
        return $this->movieRepository->create($data);
    }

    /**
     * Film güncelle
     *
     * @param int $id
     * @param array $data
     * @return Movie|null
     */
    public function updateMovie(int $id, array $data): ?Movie
    {
        return $this->movieRepository->update($id, $data);
    }

    /**
     * Film sil
     *
     * @param int $id
     * @return bool
     */
    public function deleteMovie(int $id): bool
    {
        return $this->movieRepository->delete($id);
    }

    /**
     * OMDB API'den film ara ve veritabanına ekle
     *
     * @param string $title
     * @param int $page
     * @return array
     */
    public function searchAndImportMovies(string $title, int $page = 1): array
    {
        $searchResult = $this->omdbApiService->searchByTitle($title, $page);
        
        if (!$searchResult['success']) {
            return $searchResult;
        }
        
        $movies = [];
        $importedCount = 0;
        
        foreach ($searchResult['data'] as $movieData) {
            $imdbId = $movieData['imdbID'] ?? null;
            
            if (!$imdbId) {
                continue;
            }
            
            // Film detaylarını getir
            $movieDetails = $this->omdbApiService->getMovieDetails($imdbId);
            
            if ($movieDetails['success']) {
                // Veritabanında bu IMDb ID'li film var mı kontrol et
                $existingMovie = $this->movieRepository->findByImdbId($imdbId);
                
                if ($existingMovie) {
                    // Film zaten var, güncelle
                    $existingMovie->update($movieDetails['data']);
                    $movies[] = $existingMovie->fresh();
                } else {
                    // Yeni film oluştur
                    $movie = $this->movieRepository->create($movieDetails['data']);
                    $movies[] = $movie;
                    $importedCount++;
                }
            }
        }
        
        return [
            'success' => true,
            'data' => $movies,
            'imported' => $importedCount,
            'total' => count($movies),
            'total_results' => $searchResult['total'] ?? 0,
        ];
    }

    /**
     * OMDB API'den popüler filmleri getir ve veritabanına ekle
     *
     * @param int $count
     * @return array
     */
    public function importPopularMovies(int $count = 20): array
    {
        $popularMovies = $this->omdbApiService->getPopularMovies($count);
        
        if (!$popularMovies['success']) {
            return $popularMovies;
        }
        
        $importedCount = $this->movieRepository->createMany($popularMovies['data']);
        
        return [
            'success' => true,
            'imported' => $importedCount,
            'total' => count($popularMovies['data']),
        ];
    }
} 