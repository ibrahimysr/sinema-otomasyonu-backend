<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;

class RoleController extends Controller
{
    public function changeUserRole(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Rol değiştirme başarısız.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $targetUser = User::find($request->user_id);
        $newRole = Role::find($request->role_id);

        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Bu işlem için yetkiniz bulunmamaktadır.',
            ], 403);
        }

        if ($user->hasRole('admin') && $targetUser->hasRole('super_admin')) {
            return response()->json([
                'message' => 'Süper admin rolünü değiştirme yetkiniz bulunmamaktadır.',
            ], 403);
        }

        if ($user->hasRole('admin') && $newRole->name === 'super_admin') {
            return response()->json([
                'message' => 'Süper admin rolü atama yetkiniz bulunmamaktadır.',
            ], 403);
        }

        $targetUser->role_id = $request->role_id;
        $targetUser->save();

        return response()->json([
            'message' => 'Kullanıcı rolü başarıyla güncellendi.',
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'role' => $targetUser->role->name,
            ],
        ], 200);
    }

    public function listRoles()
    {
        $roles = Role::all();
    
        return response()->json([
            'message' => 'Roller başarıyla listelendi.',
            'roles' => $roles,
        ], 200);
    }
    public function getUsersByRole(Request $request, $roleId)
    {
        $validator = \Validator::make(['role_id' => $roleId], [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz rol ID.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $users = User::where('role_id', $roleId)->get();

        return response()->json([
            'message' => 'Kullanıcılar başarıyla listelendi.',
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ], 200);
    }
}
