<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCinemaHallRequest;
use App\Http\Requests\UpdateCinemaHallRequest;
use App\Services\CinemaHallService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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

    /**
     * DataTables için salon verilerini getir
     *
     * @param Request $request
     * @return mixed
     */
    public function getHalls(Request $request)
    {
        $query = $this->cinemaHallService->getHallsQuery();

        if ($request->has('name') && $request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('cinema_id') && $request->cinema_id) {
            $query->where('cinema_id', $request->cinema_id);
        }
        if ($request->has('min_capacity') && $request->min_capacity) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        return DataTables::of($query)
            ->addColumn('cinema_name', function ($hall) {
                if ($hall->cinema) {
                    return '<div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="fas fa-building text-primary"></i>
                        </div>
                        <span>' . $hall->cinema->name . '</span>
                    </div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('type', function ($hall) {
                $badgeClass = 'bg-secondary';
                
                if ($hall->type == '2D') {
                    $badgeClass = 'bg-info';
                } else if ($hall->type == '3D') {
                    $badgeClass = 'bg-primary';
                } else if ($hall->type == 'IMAX') {
                    $badgeClass = 'bg-success';
                } else if ($hall->type == '4DX') {
                    $badgeClass = 'bg-warning';
                }
                
                return '<span class="badge ' . $badgeClass . '">
                    <i class="fas fa-film me-1"></i>' . ($hall->type ?: 'Standart') . '
                </span>';
            })
            ->addColumn('capacity', function ($hall) {
                return '<span class="badge bg-info">
                    <i class="fas fa-users me-1"></i>' . $hall->capacity . ' Kişi
                </span>';
            })
            ->addColumn('actions', function ($hall) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info edit-hall" data-id="' . $hall->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-hall" data-id="' . $hall->id . '" data-name="' . htmlspecialchars($hall->name) . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['actions', 'cinema_name', 'type', 'capacity'])
            ->make(true);
    }
} 