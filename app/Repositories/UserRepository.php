<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Tüm kullanıcıları getir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with('role')->get();
    }

    /**
     * ID'ye göre kullanıcı bul
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return $this->model->with('role')->find($id);
    }

    /**
     * E-posta adresine göre kullanıcı bul
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->with('role')->where('email', $email)->first();
    }

    /**
     * Yeni kullanıcı oluştur
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Kullanıcı güncelle
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->findById($id);
        if ($user) {
            $user->update($data);
            return $user->fresh();
        }
        return null;
    }

    /**
     * Kullanıcı sil
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }
} 