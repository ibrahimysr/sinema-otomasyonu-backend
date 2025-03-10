<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Tüm kullanıcıları getir
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * ID'ye göre kullanıcı bul
     *
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * E-posta adresine göre kullanıcı bul
     *
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Kullanıcı bilgilerini formatla
     *
     * @param Collection|User $users
     * @return array
     */
    public function formatUserData($users): array
    {
        if ($users instanceof User) {
            return [
                'id' => $users->id,
                'name' => $users->name,
                'email' => $users->email,
                'role' => [
                    'id' => $users->role->id,
                    'name' => $users->role->name,
                ],
                'created_at' => $users->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                ],
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
} 