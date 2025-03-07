<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSeatRequest;
use App\Http\Requests\UpdateSeatRequest;
use App\Services\SeatService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    protected $seatService;
    protected $responseService;

    /**
     * SeatController constructor.
     *
     * @param SeatService $seatService
     * @param ResponseService $responseService
     */
    public function __construct(SeatService $seatService, ResponseService $responseService)
    {
        $this->seatService = $seatService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm koltukları listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $seats = $this->seatService->getAllSeats();
        return $this->responseService->success($seats, 'Koltuklar başarıyla listelendi.');
    }

    /**
     * Belirli bir koltuğu göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $seat = $this->seatService->getSeatById($id);
        
        if (!$seat) {
            return $this->responseService->notFound('Koltuk bulunamadı.');
        }
        
        return $this->responseService->success($seat, 'Koltuk başarıyla bulundu.');
    }

    /**
     * Salona göre koltukları listele
     *
     * @param int $hall_id
     * @return JsonResponse
     */
    public function byHall(int $hall_id): JsonResponse
    {
        $seat = $this->seatService->getSeatByCinemaHall($hall_id);
        
        if (!$seat) {
            return $this->responseService->notFound('Bu salonda koltuk bulunamadı.');
        }
        
        return $this->responseService->success($seat, 'Salona ait koltuklar başarıyla listelendi.');
    }

    /**
     * Yeni bir koltuk oluştur
     *
     * @param StoreSeatRequest $request
     * @return JsonResponse
     */
    public function store(StoreSeatRequest $request): JsonResponse
    {
        $seat = $this->seatService->createSeat($request->validated());
        return $this->responseService->success($seat, 'Koltuk başarıyla eklendi.', 201);
    }

    /**
     * Mevcut bir koltuğu güncelle
     *
     * @param UpdateSeatRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateSeatRequest $request, int $id): JsonResponse
    {
        $seat = $this->seatService->updateSeat($id, $request->validated());
        
        if (!$seat) {
            return $this->responseService->notFound('Koltuk bulunamadı.');
        }
        
        return $this->responseService->success($seat, 'Koltuk başarıyla güncellendi.');
    }

    /**
     * Bir koltuğu sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->seatService->deleteSeat($id);
        
        if (!$result) {
            return $this->responseService->notFound('Koltuk bulunamadı.');
        }
        
        return $this->responseService->success(null, 'Koltuk başarıyla silindi.');
    }
} 