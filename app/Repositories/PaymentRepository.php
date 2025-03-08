<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    protected $model;

    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    /**
     * Tüm ödemeleri getir
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->with(['user', 'ticket'])->get();
    }

    /**
     * ID'ye göre ödeme bul
     *
     * @param int $id
     * @return Payment|null
     */
    public function findById(int $id): ?Payment
    {
        return $this->model->with(['user', 'ticket'])->find($id);
    }

    /**
     * Kullanıcı ID'sine göre ödemeleri getir
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with(['ticket'])
            ->get();
    }

    /**
     * Bilet ID'sine göre ödemeleri getir
     *
     * @param int $ticketId
     * @return Collection
     */
    public function getByTicketId(int $ticketId): Collection
    {
        return $this->model->where('ticket_id', $ticketId)
            ->with(['user'])
            ->get();
    }

    /**
     * Ödeme durumuna göre ödemeleri getir
     *
     * @param string $status
     * @return Collection
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
            ->with(['user', 'ticket'])
            ->get();
    }

    /**
     * Yeni ödeme oluştur
     *
     * @param array $data
     * @return Payment
     */
    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    /**
     * Ödeme güncelle
     *
     * @param int $id
     * @param array $data
     * @return Payment|null
     */
    public function update(int $id, array $data): ?Payment
    {
        $payment = $this->findById($id);
        if ($payment) {
            $payment->update($data);
            return $payment->fresh();
        }
        return null;
    }

    /**
     * Ödeme sil
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $payment = $this->findById($id);
        if ($payment) {
            return $payment->delete();
        }
        return false;
    }
} 