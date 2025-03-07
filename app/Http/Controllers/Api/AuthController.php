<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Role;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Kayıt başarısız.',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $role_id = 1; 
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role_id,
        ]);
    
        $token = $user->generateApiToken();
    
        return response()->json([
            'message' => 'Kayıt başarılı!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
            ],
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'E-posta alanı boş bırakılamaz.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre alanı boş bırakılamaz.',
            'password.string' => 'Şifre bir metin olmalı.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Giriş başarısız.',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $user = User::with('role')->where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json([
                'message' => 'Giriş başarısız.',
                'errors' => ['email' => ['Bu e-posta adresi kayıtlı değil.']],
            ], 401);
        }
    
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Giriş başarısız.',
                'errors' => ['password' => ['Şifre yanlış.']],
            ], 401);
        }
    
        $token = $user->generateApiToken();
    
        return response()->json([
            'message' => 'Giriş başarılı!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name
            ],
            'token' => $token,
        ]);
    }
    public function changePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Mevcut şifre alanı boş bırakılamaz.',
            'new_password.required' => 'Yeni şifre alanı boş bırakılamaz.',
            'new_password.min' => 'Yeni şifre en az 8 karakter olmalı.',
            'new_password.confirmed' => 'Yeni şifre doğrulaması eşleşmiyor.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Şifre değiştirme başarısız.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Şifre değiştirme başarısız.',
                'errors' => ['current_password' => ['Mevcut şifre yanlış.']],
            ], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $user->api_token = null;
        $user->save();

        return response()->json([
            'message' => 'Şifre başarıyla değiştirildi. Lütfen tekrar giriş yapın.',
        ], 200);
    }

    public function userProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Kullanıcı bilgileri getirildi.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    public function changeUserRole(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'role_id' => 'required|exists:roles,id',
    ], [
        'user_id.required' => 'Kullanıcı ID gereklidir.',
        'user_id.exists' => 'Geçerli bir kullanıcı ID giriniz.',
        'role_id.required' => 'Rol ID gereklidir.',
        'role_id.exists' => 'Geçerli bir rol ID giriniz.',
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

    // Yetki kontrolü
    if (!$user->isAdmin()) {
        return response()->json([
            'message' => 'Bu işlem için yetkiniz bulunmamaktadır.',
        ], 403);
    }

    // Güvenlik kontrolü: Normal admin, süper admin'i değiştiremez
    if ($user->hasRole('admin') && $targetUser->hasRole('super_admin')) {
        return response()->json([
            'message' => 'Süper admin rolünü değiştirme yetkiniz bulunmamaktadır.',
        ], 403);
    }

    // Güvenlik kontrolü: Admin, başka bir admin'i normal kullanıcı yapabilir
    // ama süper admin yapamaz (sadece süper admin yapabilir)
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
