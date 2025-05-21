<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Services\TicketService;
use App\Services\ShowtimeService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TicketController extends Controller
{
    protected $ticketService;
    protected $showtimeService;
    protected $responseService;

    /**
     * TicketController constructor.
     *
     * @param TicketService $ticketService
     * @param ShowtimeService $showtimeService
     * @param ResponseService $responseService
     */
    public function __construct(
        TicketService $ticketService,
        ShowtimeService $showtimeService,
        ResponseService $responseService
    ) {
        $this->ticketService = $ticketService;
        $this->showtimeService = $showtimeService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm biletleri listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tickets = $this->ticketService->getAllTickets();
        return $this->responseService->success($tickets, 'Biletler başarıyla listelendi.');
    }

    /**
     * Belirli bir bileti göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $ticket = $this->ticketService->getTicketById($id);
        
        if (!$ticket) {
            return $this->responseService->notFound('Bilet bulunamadı.');
        }
        
        return $this->responseService->success($ticket, 'Bilet başarıyla bulundu.');
    }

    /**
     * Bilet kodu ile bilet göster
     *
     * @param string $code
     * @return JsonResponse
     */
    public function byCode(string $code): JsonResponse
    {
        $ticket = $this->ticketService->getTicketByCode($code);
        
        if (!$ticket) {
            return $this->responseService->notFound('Bilet bulunamadı.');
        }
        
        return $this->responseService->success($ticket, 'Bilet başarıyla bulundu.');
    }

    /**
     * Kullanıcıya göre biletleri listele
     *
     * @param int $user_id
     * @return JsonResponse
     */
    public function byUser(int $user_id): JsonResponse
    {
        $tickets = $this->ticketService->getTicketsByUser($user_id);
        
        if ($tickets->isEmpty()) {
            return $this->responseService->notFound('Bu kullanıcıya ait bilet bulunamadı.');
        }
        
        return $this->responseService->success($tickets, 'Kullanıcıya ait biletler başarıyla listelendi.');
    }

    /**
     * Seansa göre biletleri listele
     *
     * @param int $showtime_id
     * @return JsonResponse
     */
    public function byShowtime(int $showtime_id): JsonResponse
    {
        $tickets = $this->ticketService->getTicketsByShowtime($showtime_id);
        
        if ($tickets->isEmpty()) {
            return $this->responseService->notFound('Bu seansa ait bilet bulunamadı.');
        }
        
        return $this->responseService->success($tickets, 'Seansa ait biletler başarıyla listelendi.');
    }

    /**
     * Yeni bir bilet oluştur
     *
     * @param StoreTicketRequest $request
     * @return JsonResponse
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $showtime = $this->showtimeService->getShowtimeById($data['showtime_id']);
        
        if (!$showtime) {
            return $this->responseService->notFound('Seans bulunamadı.');
        }
        
        $seatStatus = json_decode($showtime->seat_status, true) ?: [];
        $seatNumber = $data['seat_number'];
        
        if (!empty($seatStatus) && isset($seatStatus[$seatNumber]) && $seatStatus[$seatNumber] !== 'available') {
            \Log::warning("Müsait olmayan koltuk için bilet oluşturuluyor: Seans ID: {$showtime->id}, Koltuk: {$seatNumber}");
        }
        
        $ticket = $this->ticketService->createTicket($data);
        
        return $this->responseService->success($ticket, 'Bilet başarıyla oluşturuldu.', 201);
    }

    /**
     * Mevcut bir bileti güncelle
     *
     * @param UpdateTicketRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTicketRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        
        if (isset($data['seat_number'])) {
            $ticket = $this->ticketService->getTicketById($id);
            
            if (!$ticket) {
                return $this->responseService->notFound('Bilet bulunamadı.');
            }
            
            if ($data['seat_number'] !== $ticket->seat_number && 
                (!isset($data['showtime_id']) || $data['showtime_id'] == $ticket->showtime_id)) {
                
                $showtime = $this->showtimeService->getShowtimeById($ticket->showtime_id);
                $seatStatus = json_decode($showtime->seat_status, true) ?: [];
                $seatNumber = $data['seat_number'];
                
                if (!empty($seatStatus) && isset($seatStatus[$seatNumber]) && $seatStatus[$seatNumber] !== 'available') {
                    \Log::warning("Müsait olmayan koltuk için bilet güncelleniyor: Bilet ID: {$id}, Seans ID: {$showtime->id}, Koltuk: {$seatNumber}");
                }
            }
        }
        
        $ticket = $this->ticketService->updateTicket($id, $data);
        
        if (!$ticket) {
            return $this->responseService->notFound('Bilet bulunamadı.');
        }
        
        return $this->responseService->success($ticket, 'Bilet başarıyla güncellendi.');
    }

    /**
     * Bir bileti sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->ticketService->deleteTicket($id);
        
        if (!$result) {
            return $this->responseService->notFound('Bilet bulunamadı.');
        }
        
        return $this->responseService->success(null, 'Bilet başarıyla silindi.');
    }

    /**
     * DataTables için bilet verilerini getir
     *
     * @param Request $request
     * @return mixed
     */
    public function getTickets(Request $request)
    {
        $query = $this->ticketService->getTicketsQuery();

        if ($request->has('ticket_code') && $request->ticket_code) {
            $query->where('ticket_code', 'like', '%' . $request->ticket_code . '%');
        }
        
        if ($request->has('movie_id') && $request->movie_id) {
            $query->whereHas('showtime', function($q) use ($request) {
                $q->where('movie_id', $request->movie_id);
            });
        }
        
        if ($request->has('date') && $request->date) {
            $query->whereHas('showtime', function($q) use ($request) {
                $q->whereDate('start_time', $request->date);
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('ticket_code', function ($ticket) {
                return '<span class="badge bg-dark">
                    <i class="fas fa-barcode me-1"></i>' . $ticket->ticket_code . '
                </span>';
            })
            ->addColumn('movie', function ($ticket) {
                if ($ticket->showtime && $ticket->showtime->movie) {
                    return '<div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="fas fa-film text-primary"></i>
                        </div>
                        <span>' . $ticket->showtime->movie->title . '</span>
                    </div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('cinema', function ($ticket) {
                if ($ticket->showtime && $ticket->showtime->cinemaHall && $ticket->showtime->cinemaHall->cinema) {
                    return $ticket->showtime->cinemaHall->cinema->name;
                }
                return '-';
            })
            ->addColumn('hall', function ($ticket) {
                if ($ticket->showtime && $ticket->showtime->cinemaHall) {
                    return $ticket->showtime->cinemaHall->name;
                }
                return '-';
            })
            ->addColumn('showtime', function ($ticket) {
                if ($ticket->showtime) {
                    $startTime = new \DateTime($ticket->showtime->start_time);
                    return '<span class="badge bg-info">
                        <i class="fas fa-clock me-1"></i>' . $startTime->format('d.m.Y H:i') . '
                    </span>';
                }
                return '-';
            })
            ->addColumn('seat', function ($ticket) {
                return '<span class="badge bg-secondary">
                    <i class="fas fa-chair me-1"></i>' . $ticket->seat_number . '
                </span>';
            })
            ->addColumn('customer', function ($ticket) {
                if ($ticket->user) {
                    return $ticket->user->name;
                }
                return '-';
            })
            ->addColumn('price', function ($ticket) {
                return '<span class="badge bg-success">
                    <i class="fas fa-lira-sign me-1"></i>' . number_format($ticket->price, 2) . '
                </span>';
            })
            ->addColumn('status', function ($ticket) {
                $statusClass = 'bg-secondary';
                $statusText = 'Bilinmiyor';
                
                if ($ticket->status == 'confirmed') {
                    $statusClass = 'bg-success';
                    $statusText = 'Onaylandı';
                } else if ($ticket->status == 'reserved') {
                    $statusClass = 'bg-warning';
                    $statusText = 'Rezerve Edildi';
                } else if ($ticket->status == 'cancelled') {
                    $statusClass = 'bg-danger';
                    $statusText = 'İptal Edildi';
                }
                
                return '<span class="badge ' . $statusClass . '">
                    <i class="fas fa-check-circle me-1"></i>' . $statusText . '
                </span>';
            })
            ->addColumn('actions', function ($ticket) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info view-ticket" data-id="' . $ticket->id . '">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary edit-ticket" data-id="' . $ticket->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-ticket" data-id="' . $ticket->id . '" data-code="' . $ticket->ticket_code . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['ticket_code', 'movie', 'showtime', 'seat', 'price', 'status', 'actions'])
            ->make(true);
    }
} 