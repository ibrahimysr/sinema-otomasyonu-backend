<?php

namespace App\Repositories;

use App\Models\Showtime;
use Illuminate\Database\Eloquent\Collection;

class ShowtimeRepository
{
    protected $model;

    public function __construct(Showtime $showtime)
    {
        $this->model = $showtime;
    }

    /**
     * Tüm seansları getir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with(['movie', 'cinemaHall'])->get();
    }

    /**
     * ID'ye göre seans bul
     *
     * @param int $id
     * @return Showtime|null
     */
    public function findById(int $id): ?Showtime
    {
        return $this->model->with(['movie', 'cinemaHall'])->find($id);
    }

    /**
     * Film ID'sine göre seansları getir
     *
     * @param int $movieId
     * @return Collection
     */
    public function getByMovieId(int $movieId): Collection
    {
        return $this->model->where('movie_id', $movieId)
            ->with(['cinemaHall'])
            ->get();
    }

    /**
     * Salon ID'sine göre seansları getir
     *
     * @param int $cinemaHallId
     * @return Collection
     */
    public function getByCinemaHallId(int $cinemaHallId): Collection
    {
        return $this->model->where('cinema_hall_id', $cinemaHallId)
            ->with(['movie'])
            ->get();
    }

    /**
     * Yeni seans oluştur
     *
     * @param array $data
     * @return Showtime
     */
    public function create(array $data): Showtime
    {
        return $this->model->create($data);
    }

    /**
     * Seans güncelle
     *
     * @param int $id
     * @param array $data
     * @return Showtime|null
     */
    public function update(int $id, array $data): ?Showtime
    {
        $showtime = $this->findById($id);
        if ($showtime) {
            $showtime->update($data);
            return $showtime->fresh();
        }
        return null;
    }

    /**
     * Seans sil
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $showtime = $this->findById($id);
        if ($showtime) {
            return $showtime->delete();
        }
        return false;
    }
} 