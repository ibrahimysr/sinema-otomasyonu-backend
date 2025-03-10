<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShowtimeRequest;
use App\Http\Requests\UpdateShowtimeRequest;
use App\Services\ShowtimeService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
        $data = $request->validated();
        
        // Salon bilgisini al
        $hall = \App\Models\CinemaHall::with('seats')->find($data['cinema_hall_id']);
        
        if (!$hall) {
            return $this->responseService->error('Salon bulunamadı.', 404);
        }
        
        if (!$hall->seats) {
            return $this->responseService->error('Bu salon için koltuk bilgisi bulunamadı.', 404);
        }
        
        // Koltuk durumunu oluştur (eğer belirtilmemişse)
        if (!isset($data['seat_status'])) {
            $seatStatus = [];
            $seatData = $hall->seats->seat_data;
            
            // Eğer seat_data null ise, varsayılan bir koltuk düzeni oluştur
            if (!$seatData) {
                // Salonun kapasitesine göre basit bir koltuk düzeni oluştur
                $capacity = $hall->capacity ?: 50; // Varsayılan kapasite 50
                $rows = ceil(sqrt($capacity)); // Kare şeklinde bir düzen için
                
                $seatData = ['seats' => []];
                $seatCount = 0;
                
                for ($i = 0; $i < $rows && $seatCount < $capacity; $i++) {
                    $rowName = chr(65 + $i); // A, B, C, ...
                    $seatData['seats'][$rowName] = [];
                    
                    for ($j = 1; $j <= $rows && $seatCount < $capacity; $j++) {
                        $seatData['seats'][$rowName][] = [
                            'id' => $rowName . $j,
                            'row' => $rowName,
                            'number' => $j,
                            'status' => 'available',
                            'type' => 'normal',
                            'price' => 0,
                        ];
                        $seatCount++;
                    }
                }
            }
            
            // Koltuk durumunu oluştur
            if (isset($seatData['seats'])) {
                foreach ($seatData['seats'] as $row => $seats) {
                    foreach ($seats as $seat) {
                        // Başlangıçta tüm koltuklar müsait
                        $seatStatus[$seat['id']] = 'available';
                    }
                }
            }
            
            // Koltuk durumunu ekle
            $data['seat_status'] = json_encode($seatStatus);
            
            // Müsait koltuk sayısını hesapla (eğer belirtilmemişse)
            if (!isset($data['available_seats'])) {
                $data['available_seats'] = count($seatStatus);
            }
        }
        
        // Seansı oluştur
        $showtime = $this->showtimeService->createShowtime($data);
        
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

    /**
     * DataTables için seans verilerini getir
     *
     * @param Request $request
     * @return mixed
     */
    public function getShowtimes(Request $request)
    {
        $query = $this->showtimeService->getShowtimesQuery();

        if ($request->has('movie_id') && $request->movie_id) {
            $query->where('movie_id', $request->movie_id);
        }
        if ($request->has('cinema_id') && $request->cinema_id) {
            $query->whereHas('cinemaHall', function($q) use ($request) {
                $q->where('cinema_id', $request->cinema_id);
            });
        }
        if ($request->has('date') && $request->date) {
            $query->whereDate('start_time', $request->date);
        }

        return DataTables::of($query)
            ->addColumn('movie_title', function ($showtime) {
                if ($showtime->movie) {
                    return '<div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="fas fa-film text-primary"></i>
                        </div>
                        <span>' . $showtime->movie->title . '</span>
                    </div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('cinema_hall', function ($showtime) {
                if ($showtime->cinemaHall && $showtime->cinemaHall->cinema) {
                    return '<div>
                        <span class="d-block">' . $showtime->cinemaHall->cinema->name . '</span>
                        <small class="text-muted">' . $showtime->cinemaHall->name . '</small>
                    </div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('date', function ($showtime) {
                return '<span class="badge bg-info">
                    <i class="fas fa-calendar me-1"></i>' . $showtime->start_time->format('d.m.Y') . '
                </span>';
            })
            ->addColumn('time', function ($showtime) {
                return '<span class="badge bg-primary">
                    <i class="fas fa-clock me-1"></i>' . $showtime->start_time->format('H:i') . ' - ' . $showtime->end_time->format('H:i') . '
                </span>';
            })
            ->addColumn('status', function ($showtime) {
                $now = now();
                $badgeClass = 'bg-success';
                $statusText = 'Aktif';
                
                if ($showtime->start_time > $now) {
                    $badgeClass = 'bg-info';
                    $statusText = 'Yaklaşan';
                } else if ($showtime->end_time < $now) {
                    $badgeClass = 'bg-secondary';
                    $statusText = 'Geçmiş';
                }
                
                return '<span class="badge ' . $badgeClass . '">
                    <i class="fas fa-check-circle me-1"></i>' . $statusText . '
                </span>';
            })
            ->addColumn('actions', function ($showtime) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info edit-showtime" data-id="' . $showtime->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-showtime" data-id="' . $showtime->id . '" data-name="' . htmlspecialchars($showtime->movie->title ?? 'Seans') . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['movie_title', 'cinema_hall', 'date', 'time', 'status', 'actions'])
            ->make(true);
    }
} 