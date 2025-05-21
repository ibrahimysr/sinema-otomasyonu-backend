<?php

namespace App\Repositories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieRepository
{
    protected $model;

    public function __construct(Movie $movie)
    {
        $this->model = $movie;
    }

    /**
     * Tüm filmleri getir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Filmleri sayfalı olarak getir
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * ID'ye göre film bul
     *
     * @param int $id
     * @return Movie|null
     */
    public function findById(int $id): ?Movie
    {
        return $this->model->find($id);
    }

    /**
     * IMDb ID'sine göre film bul
     *
     * @param string $imdbId
     * @return Movie|null
     */
    public function findByImdbId(string $imdbId): ?Movie
    {
        return $this->model->where('imdb_id', $imdbId)->first();
    }

    /**
     * Başlığa göre film ara
     *
     * @param string $title
     * @return Collection
     */
    public function searchByTitle(string $title): Collection
    {
        return $this->model->where('title', 'like', "%{$title}%")->get();
    }

    /**
     * Yeni film oluştur
     *
     * @param array $data
     * @return Movie
     */
    public function create(array $data): Movie
    {
        return $this->model->create($data);
    }

    /**
     * Toplu film oluştur
     *
     * @param array $moviesData
     * @return int
     */
    public function createMany(array $moviesData): int
    {
        $count = 0;
        foreach ($moviesData as $movieData) {
            $movie = $this->findByImdbId($movieData['imdb_id']);
            if ($movie) {
                $movie->update($movieData);
            } else {
                $this->create($movieData);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Film güncelle
     *
     * @param int $id
     * @param array $data
     * @return Movie|null
     */
    public function update(int $id, array $data): ?Movie
    {
        $movie = $this->findById($id);
        if ($movie) {
            $movie->update($data);
            return $movie->fresh();
        }
        return null;
    }

    /**
     * Film sil
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $movie = $this->findById($id);
        if ($movie) {
            return $movie->delete();
        }
        return false;
    }
} 