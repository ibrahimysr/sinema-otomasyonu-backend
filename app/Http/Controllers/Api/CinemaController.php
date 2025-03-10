<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCinemaRequest;
use App\Http\Requests\UpdateCinemaRequest;
use App\Services\CinemaService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Cinema;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    protected $cinemaService;
    protected $responseService;

    public function __construct(CinemaService $cinemaService, ResponseService $responseService)
    {
        $this->cinemaService = $cinemaService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm sinemaları listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cinemas = $this->cinemaService->getAllCinemas();
        return $this->responseService->success($cinemas, 'Sinemalar başarıyla listelendi.');
    }

    /**
     * Belirli bir sinemayı göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $cinema = $this->cinemaService->getCinemaById($id);
        
        if (!$cinema) {
            return $this->responseService->notFound('Sinema bulunamadı.');
        }
        
        return $this->responseService->success($cinema, 'Sinema başarıyla bulundu.');
    }

    /**
     * Şehre göre sinemaları listele
     *
     * @param int $city_id
     * @return JsonResponse
     */
    public function byCity(int $city_id): JsonResponse
    {
        $cinemas = $this->cinemaService->getCinemasByCity($city_id);
        
        if ($cinemas->isEmpty()) {
            return $this->responseService->notFound('Bu şehirde sinema bulunamadı.');
        }
        
        return $this->responseService->success($cinemas, 'Şehre ait sinemalar başarıyla listelendi.');
    }

    /**
     * Yeni bir sinema oluştur
     *
     * @param StoreCinemaRequest $request
     * @return JsonResponse
     */
    public function store(StoreCinemaRequest $request): JsonResponse
    {
        $cinema = $this->cinemaService->createCinema($request->validated());
        return $this->responseService->success($cinema, 'Sinema başarıyla eklendi.', 201);
    }

    /**
     * Mevcut bir sinemayı güncelle
     *
     * @param UpdateCinemaRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCinemaRequest $request, int $id): JsonResponse
    {
        $cinema = $this->cinemaService->updateCinema($id, $request->validated());
        
        if (!$cinema) {
            return $this->responseService->notFound('Sinema bulunamadı.');
        }
        
        return $this->responseService->success($cinema, 'Sinema başarıyla güncellendi.');
    }

    /**
     * Bir sinemayı sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->cinemaService->deleteCinema($id);
        
        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            return $this->responseService->error(
                'Sinema silinemedi. İlişkili kayıtlar bulunmaktadır.',
                $result['related_records'],
                409
            );
        }
        
        if (!$result) {
            return $this->responseService->notFound('Sinema bulunamadı.');
        }
        
        return $this->responseService->success(null, 'Sinema başarıyla silindi.');
    }

    public function getCinemas(Request $request)
    {
        $query = Cinema::with('city'); // Şehir ilişkisi varsa

        if ($request->has('name') && $request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('city_id') && $request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        return DataTables::of($query)
            ->addColumn('name', function ($cinema) {
                return $cinema->name;
            })
            ->addColumn('city', function ($cinema) {
                return $cinema->city ? $cinema->city->name : '-';
            })
            ->addColumn('address', function ($cinema) {
                return $cinema->address ?: '-';
            })
            ->addColumn('phone', function ($cinema) {
                return $cinema->phone ?: '-';
            })
            ->addColumn('total_capacity', function ($cinema) {
                return $cinema->total_capacity ?: '0';
            })
            ->addColumn('actions', function ($cinema) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info edit-cinema" data-id="' . $cinema->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-cinema" data-id="' . $cinema->id . '" data-name="' . htmlspecialchars($cinema->name) . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}