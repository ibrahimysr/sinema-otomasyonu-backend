<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OmdbApiService
{
    protected $apiKey;
    protected $baseUrl = 'http://www.omdbapi.com/';

    public function __construct(string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? '4ad67668';
    }

    /**
     * Film adına göre arama yap
     *
     * @param string $title
     * @param int $page
     * @return array
     */
    public function searchByTitle(string $title, int $page = 1): array
    {
        $response = Http::get($this->baseUrl, [
            'apikey' => $this->apiKey,
            's' => $title,
            'page' => $page,
            'type' => 'movie',
        ]);

        if ($response->successful() && isset($response['Search'])) {
            return [
                'success' => true,
                'data' => $response['Search'],
                'total' => $response['totalResults'] ?? 0,
            ];
        }

        return [
            'success' => false,
            'message' => $response['Error'] ?? 'Film bulunamadı.',
            'data' => [],
        ];
    }

    /**
     * IMDb ID'sine göre film detaylarını getir
     *
     * @param string $imdbId
     * @return array
     */
    public function getMovieDetails(string $imdbId): array
    {
        $response = Http::get($this->baseUrl, [
            'apikey' => $this->apiKey,
            'i' => $imdbId,
            'plot' => 'full',
        ]);

        if ($response->successful() && isset($response['Title'])) {
            return [
                'success' => true,
                'data' => $this->formatMovieData($response->json()),
            ];
        }

        return [
            'success' => false,
            'message' => $response['Error'] ?? 'Film bulunamadı.',
            'data' => [],
        ];
    }

    /**
     * Popüler filmleri getir (OMDB API'si doğrudan popüler filmleri desteklemediği için,
     * önceden belirlenmiş popüler film IMDb ID'lerini kullanıyoruz)
     *
     * @param int $count
     * @return array
     */
    public function getPopularMovies(int $count = 20): array
    {
        // Popüler film IMDb ID'leri
        $popularMovieIds = [
            'tt1375666', // Inception
            'tt0816692', // Interstellar
            'tt0468569', // The Dark Knight
            'tt0133093', // The Matrix
            'tt0109830', // Forrest Gump
            'tt0110357', // The Lion King
            'tt0111161', // The Shawshank Redemption
            'tt0068646', // The Godfather
            'tt0167260', // The Lord of the Rings: The Return of the King
            'tt0120737', // The Lord of the Rings: The Fellowship of the Ring
            'tt0167261', // The Lord of the Rings: The Two Towers
            'tt0080684', // Star Wars: Episode V - The Empire Strikes Back
            'tt0076759', // Star Wars: Episode IV - A New Hope
            'tt0088763', // Back to the Future
            'tt0114369', // Se7en
            'tt0102926', // The Silence of the Lambs
            'tt0118799', // Life Is Beautiful
            'tt0120815', // Saving Private Ryan
            'tt0245429', // Spirited Away
            'tt0120689', // The Green Mile
            'tt0110413', // Léon: The Professional
            'tt0253474', // The Pianist
            'tt0172495', // Gladiator
            'tt0407887', // The Departed
            'tt0482571', // The Prestige
            'tt0209144', // Memento
            'tt0110912', // Pulp Fiction
            'tt0120586', // American History X
            'tt0114814', // The Usual Suspects
            'tt0056058', // Psycho
        ];

        // Rastgele film ID'leri seç
        $selectedIds = array_slice($popularMovieIds, 0, min($count, count($popularMovieIds)));
        
        $movies = [];
        
        foreach ($selectedIds as $imdbId) {
            $result = $this->getMovieDetails($imdbId);
            if ($result['success']) {
                $movies[] = $result['data'];
            }
        }
        
        return [
            'success' => !empty($movies),
            'data' => $movies,
            'count' => count($movies),
        ];
    }

    /**
     * OMDB API'den gelen film verisini formatla
     *
     * @param array $movieData
     * @return array
     */
    protected function formatMovieData(array $movieData): array
    {
        // Süreyi dakikaya çevir
        $duration = 0;
        if (isset($movieData['Runtime']) && preg_match('/(\d+)/', $movieData['Runtime'], $matches)) {
            $duration = (int) $matches[1];
        }

        // Yayın tarihini formatla
        $releaseDate = null;
        if (isset($movieData['Released']) && $movieData['Released'] !== 'N/A') {
            try {
                $releaseDate = Carbon::parse($movieData['Released'])->format('Y-m-d');
            } catch (\Exception $e) {
                $releaseDate = null;
            }
        }

        return [
            'title' => $movieData['Title'] ?? '',
            'description' => $movieData['Plot'] ?? '',
            'genre' => $movieData['Genre'] ?? '',
            'duration' => $duration,
            'poster_url' => ($movieData['Poster'] && $movieData['Poster'] !== 'N/A') ? $movieData['Poster'] : null,
            'language' => $movieData['Language'] ?? '',
            'release_date' => $releaseDate,
            'is_in_theaters' => true, // Varsayılan olarak gösterimde
            'imdb_id' => $movieData['imdbID'] ?? '',
            'imdb_rating' => isset($movieData['imdbRating']) && $movieData['imdbRating'] !== 'N/A' ? (float) $movieData['imdbRating'] : null,
        ];
    }
} 