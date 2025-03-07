<?php

namespace App\Repositories;

use App\Models\CinemaHall;
use Illuminate\Database\Eloquent\Collection;

class CinemaHallRepository
{
    protected $model;

    public function __construct(CinemaHall $cinemaHall)
    {
        $this->model = $cinemaHall;
    }

    /**
     * Tüm salonları getir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with('cinema')->get();
    }

    /**
     * ID'ye göre salon bul
     *
     * @param int $id
     * @return CinemaHall|null
     */
    public function findById(int $id): ?CinemaHall
    {
        return $this->model->with('cinema')->find($id);
    }

    /**
     * Sinema ID'sine göre salonları getir
     *
     * @param int $cinemaId
     * @return Collection
     */
    public function getByCinemaId(int $cinemaId): Collection
    {
        return $this->model->where('cinema_id', $cinemaId)->get();
    }

    /**
     * Yeni salon oluştur
     *
     * @param array $data
     * @return CinemaHall
     */
    public function create(array $data): CinemaHall
    {
        return $this->model->create($data);
    }

    /**
     * Salon güncelle
     *
     * @param int $id
     * @param array $data
     * @return CinemaHall|null
     */
    public function update(int $id, array $data): ?CinemaHall
    {
        $hall = $this->findById($id);
        if ($hall) {
            $hall->update($data);
            return $hall->fresh();
        }
        return null;
    }

    /**
     * Salon sil
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $hall = $this->findById($id);
        if ($hall) {
            return $hall->delete();
        }
        return false;
    }
} 