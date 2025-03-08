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
        
        // Seans bilgisini al
        $showtime = $this->showtimeService->getShowtimeById($data['showtime_id']);
        
        if (!$showtime) {
            return $this->responseService->notFound('Seans bulunamadı.');
        }
        
        // Koltuk müsait mi kontrol et
        $seatStatus = json_decode($showtime->seat_status, true) ?: [];
        $seatNumber = $data['seat_number'];
        
        if (!isset($seatStatus[$seatNumber]) || $seatStatus[$seatNumber] !== 'available') {
            return $this->responseService->error('Seçilen koltuk müsait değil.', 400);
        }
        
        // Bilet oluştur
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
        
        // Eğer koltuk değişiyorsa, müsait mi kontrol et
        if (isset($data['seat_number'])) {
            $ticket = $this->ticketService->getTicketById($id);
            
            if (!$ticket) {
                return $this->responseService->notFound('Bilet bulunamadı.');
            }
            
            // Eğer koltuk değişiyorsa ve aynı seans ise kontrol et
            if ($data['seat_number'] !== $ticket->seat_number && 
                (!isset($data['showtime_id']) || $data['showtime_id'] == $ticket->showtime_id)) {
                
                $showtime = $this->showtimeService->getShowtimeById($ticket->showtime_id);
                $seatStatus = json_decode($showtime->seat_status, true) ?: [];
                $seatNumber = $data['seat_number'];
                
                if (!isset($seatStatus[$seatNumber]) || $seatStatus[$seatNumber] !== 'available') {
                    return $this->responseService->error('Seçilen koltuk müsait değil.', 400);
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
} 