<?php

namespace App\Services;

use App\Models\CinemaHall;
use App\Repositories\CinemaHallRepository;
use Illuminate\Database\Eloquent\Collection;

class CinemaHallService
{
    protected $cinemaHallRepository;

    public function __construct(CinemaHallRepository $cinemaHallRepository)
    {
        $this->cinemaHallRepository = $cinemaHallRepository;
    }

    /**
     * Tüm salonları getir
     *
     * @return Collection
     */
    public function getAllHalls(): Collection
    {
        return $this->cinemaHallRepository->getAll();
    }

    /**
     * ID'ye göre salon bul
     *
     * @param int $id
     * @return CinemaHall|null
     */
    public function getHallById(int $id): ?CinemaHall
    {
        return $this->cinemaHallRepository->findById($id);
    }

    /**
     * Sinema ID'sine göre salonları getir
     *
     * @param int $cinemaId
     * @return Collection
     */
    public function getHallsByCinema(int $cinemaId): Collection
    {
        return $this->cinemaHallRepository->getByCinemaId($cinemaId);
    }

    /**
     * Yeni salon oluştur
     *
     * @param array $data
     * @return CinemaHall
     */
    public function createHall(array $data): CinemaHall
    {
        return $this->cinemaHallRepository->create($data);
    }

    /**
     * Salon güncelle
     *
     * @param int $id
     * @param array $data
     * @return CinemaHall|null
     */
    public function updateHall(int $id, array $data): ?CinemaHall
    {
        return $this->cinemaHallRepository->update($id, $data);
    }

    /**
     * Salon sil
     *
     * @param int $id
     * @return bool
     */
    public function deleteHall(int $id): bool
    {
        return $this->cinemaHallRepository->delete($id);
    }
} 