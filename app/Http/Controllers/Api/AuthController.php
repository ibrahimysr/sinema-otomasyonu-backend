<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;
    protected $responseService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     * @param ResponseService $responseService
     */
    public function __construct(AuthService $authService, ResponseService $responseService)
    {
        $this->authService = $authService;
        $this->responseService = $responseService;
    }

   
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());
        
        return $this->responseService->success(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'token' => $user->api_token,
            ],
            'Kayıt başarılı!',
            201
        );
    }

    /**
     * Kullanıcı girişi yap
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request->email, $request->password);
        
        if (!$user) {
            return $this->responseService->error(
                'Giriş başarısız.',
                ['email' => ['E-posta veya şifre yanlış.']],
                401
            );
        }
        
        return $this->responseService->success(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name,
                'token' => $user->api_token,
            ],
            'Giriş başarılı!'
        );
    }

    /**
     * Kullanıcı şifresini değiştir
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $result = $this->authService->changePassword(
            $request->user(),
            $request->current_password,
            $request->new_password
        );
        
        if (!$result) {
            return $this->responseService->error(
                'Şifre değiştirme başarısız.',
                ['current_password' => ['Mevcut şifre yanlış.']],
                401
            );
        }
        
        return $this->responseService->success(
            null,
            'Şifre başarıyla değiştirildi. Lütfen tekrar giriş yapın.'
        );
    }

    /**
     * Kullanıcı profilini getir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userProfile(Request $request): JsonResponse
    {
        $profileData = $this->authService->getUserProfile($request->user());
        
        return $this->responseService->success(
            $profileData,
            'Kullanıcı bilgileri getirildi.'
        );
    }
}
