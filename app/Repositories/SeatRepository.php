<?php

namespace App\Repositories;

use App\Models\Seat;
use Illuminate\Database\Eloquent\Collection;

class SeatRepository
{
    protected $model;

    public function __construct(Seat $seat)
    {
        $this->model = $seat;
    }

    /**
     * Tüm koltukları getir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with('cinemaHall')->get();
    }

    /**
     * ID'ye göre koltuk bul
     *
     * @param int $id
     * @return Seat|null
     */
    public function findById(int $id): ?Seat
    {
        return $this->model->with('cinemaHall')->find($id);
    }

    /**
     * Salon ID'sine göre koltukları getir
     *
     * @param int $hallId
     * @return Seat|null
     */
    public function getByCinemaHallId(int $hallId): ?Seat
    {
        return $this->model->where('cinema_hall_id', $hallId)->first();
    }

    /**
     * Yeni koltuk oluştur
     *
     * @param array $data
     * @return Seat
     */
    public function create(array $data): Seat
    {
        return $this->model->create($data);
    }

    /**
     * Koltuk güncelle
     *
     * @param int $id
     * @param array $data
     * @return Seat|null
     */
    public function update(int $id, array $data): ?Seat
    {
        $seat = $this->findById($id);
        if ($seat) {
            $seat->update($data);
            return $seat->fresh();
        }
        return null;
    }

    /**
     * Koltuk sil
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $seat = $this->findById($id);
        if ($seat) {
            return $seat->delete();
        }
        return false;
    }
} 