<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCinemaHallRequest;
use App\Http\Requests\UpdateCinemaHallRequest;
use App\Services\CinemaHallService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CinemaHallController extends Controller
{
    protected $cinemaHallService;
    protected $responseService;

    /**
     * CinemaHallController constructor.
     *
     * @param CinemaHallService $cinemaHallService
     * @param ResponseService $responseService
     */
    public function __construct(CinemaHallService $cinemaHallService, ResponseService $responseService)
    {
        $this->cinemaHallService = $cinemaHallService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm salonları listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $halls = $this->cinemaHallService->getAllHalls();
        return $this->responseService->success($halls, 'Salonlar başarıyla listelendi.');
    }

    /**
     * Belirli bir salonu göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $hall = $this->cinemaHallService->getHallById($id);
        
        if (!$hall) {
            return $this->responseService->notFound('Salon bulunamadı.');
        }
        
        return $this->responseService->success($hall, 'Salon başarıyla bulundu.');
    }

    /**
     * Sinemaya göre salonları listele
     *
     * @param int $cinema_id
     * @return JsonResponse
     */
    public function byCinema(int $cinema_id): JsonResponse
    {
        $halls = $this->cinemaHallService->getHallsByCinema($cinema_id);
        
        if ($halls->isEmpty()) {
            return $this->responseService->notFound('Bu sinemada salon bulunamadı.');
        }
        
        return $this->responseService->success($halls, 'Sinemaya ait salonlar başarıyla listelendi.');
    }

    /**
     * Yeni bir salon oluştur
     *
     * @param StoreCinemaHallRequest $request
     * @return JsonResponse
     */
    public function store(StoreCinemaHallRequest $request): JsonResponse
    {
        $hall = $this->cinemaHallService->createHall($request->validated());
        return $this->responseService->success($hall, 'Salon başarıyla eklendi.', 201);
    }

    /**
     * Mevcut bir salonu güncelle
     *
     * @param UpdateCinemaHallRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCinemaHallRequest $request, int $id): JsonResponse
    {
        $hall = $this->cinemaHallService->updateHall($id, $request->validated());
        
        if (!$hall) {
            return $this->responseService->notFound('Salon bulunamadı.');
        }
        
        return $this->responseService->success($hall, 'Salon başarıyla güncellendi.');
    }

    /**
     * Bir salonu sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->cinemaHallService->deleteHall($id);
        
        if (!$result) {
            return $this->responseService->notFound('Salon bulunamadı.');
        }
        
        return $this->responseService->success(null, 'Salon başarıyla silindi.');
    }
} 