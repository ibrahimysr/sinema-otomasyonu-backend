<?php

namespace App\Repositories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Collection;

class CinemaRepository
{
    protected $model;

    public function __construct(Cinema $cinema)
    {
        $this->model = $cinema;
    }

    public function getAll(): Collection
    {
        return $this->model->with('city')->get();
    }

    public function findById(int $id): ?Cinema
    {
        return $this->model->with('city')->find($id);
    }

    public function getByCityId(int $cityId): Collection
    {
        return $this->model->where('city_id', $cityId)->with('city')->get();
    }

    public function create(array $data): Cinema
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Cinema
    {
        $cinema = $this->findById($id);
        if ($cinema) {
            $cinema->update($data);
            return $cinema->fresh();
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $cinema = $this->findById($id);
        if ($cinema) {
            return $cinema->delete();
        }
        return false;
    }
} 