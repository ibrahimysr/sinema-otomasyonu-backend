<?php

namespace App\Repositories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository
{
    protected $model;

    public function __construct(Ticket $ticket)
    {
        $this->model = $ticket;
    }

    /**
     * Tüm biletleri getir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with(['user', 'showtime'])->get();
    }

    /**
     * ID'ye göre bilet bul
     *
     * @param int $id
     * @return Ticket|null
     */
    public function findById(int $id): ?Ticket
    {
        return $this->model->with(['user', 'showtime'])->find($id);
    }

    /**
     * Bilet kodu ile bilet bul
     *
     * @param string $ticketCode
     * @return Ticket|null
     */
    public function findByTicketCode(string $ticketCode): ?Ticket
    {
        return $this->model->where('ticket_code', $ticketCode)
            ->with(['user', 'showtime'])
            ->first();
    }

    /**
     * Kullanıcı ID'sine göre biletleri getir
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with(['showtime'])
            ->get();
    }

    /**
     * Seans ID'sine göre biletleri getir
     *
     * @param int $showtimeId
     * @return Collection
     */
    public function getByShowtimeId(int $showtimeId): Collection
    {
        return $this->model->where('showtime_id', $showtimeId)
            ->with(['user'])
            ->get();
    }

    /**
     * Yeni bilet oluştur
     *
     * @param array $data
     * @return Ticket
     */
    public function create(array $data): Ticket
    {
        return $this->model->create($data);
    }

    /**
     * Bilet güncelle
     *
     * @param int $id
     * @param array $data
     * @return Ticket|null
     */
    public function update(int $id, array $data): ?Ticket
    {
        $ticket = $this->findById($id);
        if ($ticket) {
            $ticket->update($data);
            return $ticket->fresh();
        }
        return null;
    }

    /**
     * Bilet sil
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $ticket = $this->findById($id);
        if ($ticket) {
            return $ticket->delete();
        }
        return false;
    }
} 