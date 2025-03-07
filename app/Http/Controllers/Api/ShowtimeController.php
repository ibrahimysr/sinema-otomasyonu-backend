<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShowtimeRequest;
use App\Http\Requests\UpdateShowtimeRequest;
use App\Services\ShowtimeService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    protected $showtimeService;
    protected $responseService;

    /**
     * ShowtimeController constructor.
     *
     * @param ShowtimeService $showtimeService
     * @param ResponseService $responseService
     */
    public function __construct(ShowtimeService $showtimeService, ResponseService $responseService)
    {
        $this->showtimeService = $showtimeService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm seansları listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $showtimes = $this->showtimeService->getAllShowtimes();
        return $this->responseService->success($showtimes, 'Seanslar başarıyla listelendi.');
    }

    /**
     * Belirli bir seansı göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $showtime = $this->showtimeService->getShowtimeById($id);
        
        if (!$showtime) {
            return $this->responseService->notFound('Seans bulunamadı.');
        }
        
        return $this->responseService->success($showtime, 'Seans başarıyla bulundu.');
    }

    /**
     * Filme göre seansları listele
     *
     * @param int $movie_id
     * @return JsonResponse
     */
    public function byMovie(int $movie_id): JsonResponse
    {
        $showtimes = $this->showtimeService->getShowtimesByMovie($movie_id);
        
        if ($showtimes->isEmpty()) {
            return $this->responseService->notFound('Bu filme ait seans bulunamadı.');
        }
        
        return $this->responseService->success($showtimes, 'Filme ait seanslar başarıyla listelendi.');
    }

    /**
     * Salona göre seansları listele
     *
     * @param int $cinema_hall_id
     * @return JsonResponse
     */
    public function byCinemaHall(int $cinema_hall_id): JsonResponse
    {
        $showtimes = $this->showtimeService->getShowtimesByCinemaHall($cinema_hall_id);
        
        if ($showtimes->isEmpty()) {
            return $this->responseService->notFound('Bu salona ait seans bulunamadı.');
        }
        
        return $this->responseService->success($showtimes, 'Salona ait seanslar başarıyla listelendi.');
    }

    /**
     * Yeni bir seans oluştur
     *
     * @param StoreShowtimeRequest $request
     * @return JsonResponse
     */
    public function store(StoreShowtimeRequest $request): JsonResponse
    {
        $showtime = $this->showtimeService->createShowtime($request->validated());
        return $this->responseService->success($showtime, 'Seans başarıyla eklendi.', 201);
    }

    /**
     * Mevcut bir seansı güncelle
     *
     * @param UpdateShowtimeRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateShowtimeRequest $request, int $id): JsonResponse
    {
        $showtime = $this->showtimeService->updateShowtime($id, $request->validated());
        
        if (!$showtime) {
            return $this->responseService->notFound('Seans bulunamadı.');
        }
        
        return $this->responseService->success($showtime, 'Seans başarıyla güncellendi.');
    }

    /**
     * Bir seansı sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->showtimeService->deleteShowtime($id);
        
        if (!$result) {
            return $this->responseService->notFound('Seans bulunamadı.');
        }
        
        return $this->responseService->success(null, 'Seans başarıyla silindi.');
    }
} 