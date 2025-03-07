<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeUserRoleRequest;
use App\Http\Requests\GetUsersByRoleRequest;
use App\Services\RoleService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;
    protected $responseService;

    /**
     * RoleController constructor.
     *
     * @param RoleService $roleService
     * @param ResponseService $responseService
     */
    public function __construct(RoleService $roleService, ResponseService $responseService)
    {
        $this->roleService = $roleService;
        $this->responseService = $responseService;
    }

    /**
     * Kullanıcının rolünü değiştir
     *
     * @param ChangeUserRoleRequest $request
     * @return JsonResponse
     */
    public function changeUserRole(ChangeUserRoleRequest $request): JsonResponse
    {
        $result = $this->roleService->changeUserRole(
            $request->user(),
            $request->user_id,
            $request->role_id
        );
        
        if (!$result['success']) {
            return $this->responseService->error(
                $result['message'],
                null,
                $result['status']
            );
        }
        
        return $this->responseService->success(
            $result['user'],
            'Kullanıcı rolü başarıyla güncellendi.'
        );
    }

    /**
     * Tüm rolleri listele
     *
     * @return JsonResponse
     */
    public function listRoles(): JsonResponse
    {
        $roles = $this->roleService->getAllRoles();
        
        return $this->responseService->success(
            $roles,
            'Roller başarıyla listelendi.'
        );
    }

    /**
     * Belirli bir role sahip kullanıcıları getir
     *
     * @param GetUsersByRoleRequest $request
     * @return JsonResponse
     */
    public function getUsersByRole(GetUsersByRoleRequest $request): JsonResponse
    {
        $users = $this->roleService->getUsersByRole($request->role_id);
        $formattedUsers = $this->roleService->formatUserData($users);
        
        return $this->responseService->success(
            $formattedUsers,
            'Kullanıcılar başarıyla listelendi.'
        );
    }
}
