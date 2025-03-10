<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;
    protected $responseService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     * @param ResponseService $responseService
     */
    public function __construct(UserService $userService, ResponseService $responseService)
    {
        $this->userService = $userService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm kullanıcıları listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        $formattedUsers = $this->userService->formatUserData($users);
        
        return $this->responseService->success(
            $formattedUsers,
            'Kullanıcılar başarıyla listelendi.'
        );
    }
} 