<?php

namespace App\Services;

use App\Models\CinemaHall;
use App\Repositories\CinemaHallRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

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
        // Salonu oluştur
        $hall = $this->cinemaHallRepository->create($data);
        
        // Salon için koltukları otomatik olarak oluştur
        $seatService = app(SeatService::class);
        $seatData = [
            'cinema_hall_id' => $hall->id,
            'status' => 'active'
        ];
        
        $seatService->createSeat($seatData);
        
        return $hall;
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

    /**
     * DataTables için salon sorgusu oluştur
     *
     * @return Builder
     */
    public function getHallsQuery(): Builder
    {
        return CinemaHall::with('cinema');
    }
} 