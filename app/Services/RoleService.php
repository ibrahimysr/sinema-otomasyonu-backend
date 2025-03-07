<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    /**
     * Kullanıcının rolünü değiştir
     *
     * @param User $currentUser
     * @param int $targetUserId
     * @param int $newRoleId
     * @return array|bool
     */
    public function changeUserRole(User $currentUser, int $targetUserId, int $newRoleId)
    {
        $targetUser = User::find($targetUserId);
        $newRole = Role::find($newRoleId);
        
        // Yetki kontrolü
        if (!$currentUser->isAdmin()) {
            return [
                'success' => false,
                'message' => 'Bu işlem için yetkiniz bulunmamaktadır.',
                'status' => 403
            ];
        }
        
        // Admin, süper admin rolünü değiştiremez
        if ($currentUser->hasRole('admin') && $targetUser->hasRole('super_admin')) {
            return [
                'success' => false,
                'message' => 'Süper admin rolünü değiştirme yetkiniz bulunmamaktadır.',
                'status' => 403
            ];
        }
        
        // Admin, süper admin rolü atayamaz
        if ($currentUser->hasRole('admin') && $newRole->name === 'super_admin') {
            return [
                'success' => false,
                'message' => 'Süper admin rolü atama yetkiniz bulunmamaktadır.',
                'status' => 403
            ];
        }
        
        // Rol değiştirme işlemi
        $targetUser->role_id = $newRoleId;
        $targetUser->save();
        
        return [
            'success' => true,
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'role' => $targetUser->role->name,
            ]
        ];
    }
    
    /**
     * Tüm rolleri listele
     *
     * @return Collection
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }
    
    /**
     * Belirli bir role sahip kullanıcıları getir
     *
     * @param int $roleId
     * @return Collection
     */
    public function getUsersByRole(int $roleId): Collection
    {
        return User::where('role_id', $roleId)->get();
    }
    
    /**
     * Kullanıcı bilgilerini formatla
     *
     * @param Collection $users
     * @return array
     */
    public function formatUserData(Collection $users): array
    {
        return $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
} 