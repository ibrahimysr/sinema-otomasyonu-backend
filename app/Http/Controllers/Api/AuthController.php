<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            'data' => [
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
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Giriş başarısız.',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $user = User::with('role')->where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Giriş başarısız.',
                'errors' => ['email' => ['E-posta veya şifre yanlış.']],
            ], 401);
        }
    
        $token = $user->generateApiToken();
    
        return response()->json([
            'message' => 'Giriş başarılı!',
            'data' => [
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
                'role_id' => $user->role_id,
                'role_name' => $user->role->name,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }
}
