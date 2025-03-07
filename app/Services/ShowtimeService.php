<?php

namespace App\Services;

use App\Models\Showtime;
use App\Repositories\ShowtimeRepository;
use Illuminate\Database\Eloquent\Collection;

class ShowtimeService
{
    protected $showtimeRepository;

    public function __construct(ShowtimeRepository $showtimeRepository)
    {
        $this->showtimeRepository = $showtimeRepository;
    }

    /**
     * Tüm seansları getir
     *
     * @return Collection
     */
    public function getAllShowtimes(): Collection
    {
        return $this->showtimeRepository->getAll();
    }

    /**
     * ID'ye göre seans bul
     *
     * @param int $id
     * @return Showtime|null
     */
    public function getShowtimeById(int $id): ?Showtime
    {
        return $this->showtimeRepository->findById($id);
    }

    /**
     * Film ID'sine göre seansları getir
     *
     * @param int $movieId
     * @return Collection
     */
    public function getShowtimesByMovie(int $movieId): Collection
    {
        return $this->showtimeRepository->getByMovieId($movieId);
    }

    /**
     * Salon ID'sine göre seansları getir
     *
     * @param int $cinemaHallId
     * @return Collection
     */
    public function getShowtimesByCinemaHall(int $cinemaHallId): Collection
    {
        return $this->showtimeRepository->getByCinemaHallId($cinemaHallId);
    }

    /**
     * Yeni seans oluştur
     *
     * @param array $data
     * @return Showtime
     */
    public function createShowtime(array $data): Showtime
    {
        return $this->showtimeRepository->create($data);
    }

    /**
     * Seans güncelle
     *
     * @param int $id
     * @param array $data
     * @return Showtime|null
     */
    public function updateShowtime(int $id, array $data): ?Showtime
    {
        return $this->showtimeRepository->update($id, $data);
    }

    /**
     * Seans sil
     *
     * @param int $id
     * @return bool
     */
    public function deleteShowtime(int $id): bool
    {
        return $this->showtimeRepository->delete($id);
    }
} 