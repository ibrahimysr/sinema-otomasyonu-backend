<?php

namespace App\Services;

use App\Models\Showtime;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Repositories\ShowtimeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TicketService
{
    protected $ticketRepository;
    protected $showtimeRepository;

    public function __construct(TicketRepository $ticketRepository, ShowtimeRepository $showtimeRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->showtimeRepository = $showtimeRepository;
    }

    /**
     * Tüm biletleri getir
     *
     * @return Collection
     */
    public function getAllTickets(): Collection
    {
        return $this->ticketRepository->getAll();
    }

    /**
     * ID'ye göre bilet bul
     *
     * @param int $id
     * @return Ticket|null
     */
    public function getTicketById(int $id): ?Ticket
    {
        return $this->ticketRepository->findById($id);
    }

    /**
     * Bilet kodu ile bilet bul
     *
     * @param string $ticketCode
     * @return Ticket|null
     */
    public function getTicketByCode(string $ticketCode): ?Ticket
    {
        return $this->ticketRepository->findByTicketCode($ticketCode);
    }

    /**
     * Kullanıcı ID'sine göre biletleri getir
     *
     * @param int $userId
     * @return Collection
     */
    public function getTicketsByUser(int $userId): Collection
    {
        return $this->ticketRepository->getByUserId($userId);
    }

    /**
     * Seans ID'sine göre biletleri getir
     *
     * @param int $showtimeId
     * @return Collection
     */
    public function getTicketsByShowtime(int $showtimeId): Collection
    {
        return $this->ticketRepository->getByShowtimeId($showtimeId);
    }

    /**
     * Yeni bilet oluştur
     *
     * @param array $data
     * @return Ticket
     */
    public function createTicket(array $data): Ticket
    {
        if (!isset($data['ticket_code'])) {
            $data['ticket_code'] = $this->generateTicketCode();
        }
        
        $showtime = $this->showtimeRepository->findById($data['showtime_id']);
        
        if (!isset($data['price']) && $showtime) {
            $data['price'] = $showtime->price;
        }
        
        $ticket = $this->ticketRepository->create($data);
        
        if ($showtime) {
            $this->updateShowtimeSeatStatus($showtime, $data['seat_number'], 'sold');
        }
        
        return $ticket;
    }

    /**
     * Bilet güncelle
     *
     * @param int $id
     * @param array $data
     * @return Ticket|null
     */
    public function updateTicket(int $id, array $data): ?Ticket
    {
        $ticket = $this->ticketRepository->findById($id);
        
        if ($ticket && isset($data['seat_number']) && $data['seat_number'] !== $ticket->seat_number) {
            $showtime = $ticket->showtime;
            
            $this->updateShowtimeSeatStatus($showtime, $ticket->seat_number, 'available');
            
            $this->updateShowtimeSeatStatus($showtime, $data['seat_number'], 'sold');
        }
        
        // Eğer durum değişiyorsa ve iptal ediliyorsa, koltuğu müsait yap
        if ($ticket && isset($data['status']) && $data['status'] === 'cancelled' && $ticket->status !== 'cancelled') {
            $showtime = $ticket->showtime;
            $this->updateShowtimeSeatStatus($showtime, $ticket->seat_number, 'available');
        }
        
        return $this->ticketRepository->update($id, $data);
    }

    /**
     * Bilet sil
     *
     * @param int $id
     * @return bool
     */
    public function deleteTicket(int $id): bool
    {
        $ticket = $this->ticketRepository->findById($id);
        
        // Eğer bilet bulunduysa, seans koltuk durumunu güncelle
        if ($ticket) {
            $showtime = $ticket->showtime;
            $this->updateShowtimeSeatStatus($showtime, $ticket->seat_number, 'available');
        }
        
        return $this->ticketRepository->delete($id);
    }

    /**
     * Benzersiz bilet kodu oluştur
     *
     * @return string
     */
    private function generateTicketCode(): string
    {
        $code = strtoupper(Str::random(8));
        
        // Eğer kod zaten varsa, yeniden oluştur
        while ($this->ticketRepository->findByTicketCode($code)) {
            $code = strtoupper(Str::random(8));
        }
        
        return $code;
    }

    /**
     * Seans koltuk durumunu güncelle
     *
     * @param Showtime $showtime
     * @param string $seatNumber
     * @param string $status
     * @return void
     */
    private function updateShowtimeSeatStatus(Showtime $showtime, string $seatNumber, string $status): void
    {
        $seatStatus = json_decode($showtime->seat_status, true) ?: [];
        $oldStatus = $seatStatus[$seatNumber] ?? null;
        
        $seatStatus[$seatNumber] = $status;
        
        $availableSeats = $showtime->available_seats;
        
        if ($oldStatus === 'available' && $status !== 'available') {
            $availableSeats--;
        } elseif ($oldStatus !== 'available' && $status === 'available') {
            $availableSeats++;
        }
        
        $this->showtimeRepository->update($showtime->id, [
            'seat_status' => json_encode($seatStatus),
            'available_seats' => $availableSeats,
        ]);
    }

    /**
     * DataTables için bilet sorgusu oluştur
     *
     * @return Builder
     */
    public function getTicketsQuery(): Builder
    {
        return Ticket::with(['user', 'showtime.movie', 'showtime.cinemaHall.cinema']);
    }
} 