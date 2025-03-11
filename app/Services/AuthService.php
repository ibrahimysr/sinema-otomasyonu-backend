<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Kullanıcı kaydı oluştur
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $role_id = 1; 
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $role_id,
        ]);
        
        $user->generateApiToken();
        
        return $user;
    }
    
    /**
     * Kullanıcı girişi yap
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function login(string $email, string $password): ?User
    {
        $user = User::with('role')->where('email', $email)->first();
        
        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }
        
        $user->generateApiToken();
        
        return $user;
    }
    
    /**
     * Kullanıcı şifresini değiştir
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }
        
        $user->password = Hash::make($newPassword);
        $user->save();
        
        $user->api_token = null;
        $user->save();
        
        return true;
    }
    
    /**
     * Kullanıcı profilini getir
     *
     * @param User $user
     * @return array
     */
    public function getUserProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
} 