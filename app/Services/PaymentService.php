<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Ticket;
use App\Repositories\PaymentRepository;
use App\Repositories\TicketRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $paymentRepository;
    protected $ticketRepository;

    public function __construct(PaymentRepository $paymentRepository, TicketRepository $ticketRepository)
    {
        $this->paymentRepository = $paymentRepository;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Tüm ödemeleri getir
     *
     * @return Collection
     */
    public function getAllPayments(): Collection
    {
        return $this->paymentRepository->getAll();
    }

    /**
     * ID'ye göre ödeme bul
     *
     * @param int $id
     * @return Payment|null
     */
    public function getPaymentById(int $id): ?Payment
    {
        return $this->paymentRepository->findById($id);
    }

    /**
     * Kullanıcı ID'sine göre ödemeleri getir
     *
     * @param int $userId
     * @return Collection
     */
    public function getPaymentsByUser(int $userId): Collection
    {
        return $this->paymentRepository->getByUserId($userId);
    }

    /**
     * Bilet ID'sine göre ödemeleri getir
     *
     * @param int $ticketId
     * @return Collection
     */
    public function getPaymentsByTicket(int $ticketId): Collection
    {
        return $this->paymentRepository->getByTicketId($ticketId);
    }

    /**
     * Ödeme durumuna göre ödemeleri getir
     *
     * @param string $status
     * @return Collection
     */
    public function getPaymentsByStatus(string $status): Collection
    {
        return $this->paymentRepository->getByStatus($status);
    }

    /**
     * Yeni ödeme oluştur
     *
     * @param array $data
     * @return Payment
     */
    public function createPayment(array $data): Payment
    {
        // Bilet bilgisini al
        if (isset($data['ticket_id'])) {
            $ticket = $this->ticketRepository->findById($data['ticket_id']);
            
            // Eğer tutar belirtilmemişse, bilet fiyatını kullan
            if (!isset($data['amount']) && $ticket) {
                $data['amount'] = $ticket->price;
            }
            
            // Eğer kullanıcı belirtilmemişse, bilet sahibini kullan
            if (!isset($data['user_id']) && $ticket) {
                $data['user_id'] = $ticket->user_id;
            }
        }
        
        // Ödeme oluştur
        $payment = $this->paymentRepository->create($data);
        
        // Eğer ödeme başarılıysa ve bilet varsa, bilet durumunu güncelle
        if ($payment->status === 'completed' && isset($data['ticket_id'])) {
            $this->updateTicketStatus($data['ticket_id'], 'confirmed');
        }
        
        return $payment;
    }

    /**
     * Ödeme güncelle
     *
     * @param int $id
     * @param array $data
     * @return Payment|null
     */
    public function updatePayment(int $id, array $data): ?Payment
    {
        $payment = $this->paymentRepository->findById($id);
        
        // Eğer ödeme durumu değişiyorsa ve tamamlandıysa, bilet durumunu güncelle
        if ($payment && isset($data['status']) && $data['status'] === 'completed' && $payment->status !== 'completed') {
            $this->updateTicketStatus($payment->ticket_id, 'confirmed');
        }
        
        // Eğer ödeme durumu değişiyorsa ve başarısızsa, bilet durumunu güncelle
        if ($payment && isset($data['status']) && $data['status'] === 'failed' && $payment->status !== 'failed') {
            $this->updateTicketStatus($payment->ticket_id, 'reserved');
        }
        
        return $this->paymentRepository->update($id, $data);
    }

    /**
     * Ödeme sil
     *
     * @param int $id
     * @return bool
     */
    public function deletePayment(int $id): bool
    {
        return $this->paymentRepository->delete($id);
    }

    /**
     * Bilet durumunu güncelle
     *
     * @param int $ticketId
     * @param string $status
     * @return void
     */
    private function updateTicketStatus(int $ticketId, string $status): void
    {
        $this->ticketRepository->update($ticketId, [
            'status' => $status
        ]);
    }

    /**
     * Ödemeler için sorgu oluştur (DataTables için)
     *
     * @return Builder
     */
    public function getPaymentsQuery(): Builder
    {
        return Payment::with(['user', 'ticket'])
            ->select('payments.*');
    }
    
    /**
     * Ödeme istatistiklerini hesapla
     *
     * @return array
     */
    public function getPaymentStats(): array
    {
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'cancelled' => Payment::where('status', 'cancelled')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'today_amount' => Payment::where('status', 'completed')
                ->whereDate('created_at', now()->toDateString())
                ->sum('amount'),
            'payment_methods' => [
                'credit_card' => Payment::where('payment_method', 'credit_card')->count(),
                'cash' => Payment::where('payment_method', 'cash')->count(),
                'bank_transfer' => Payment::where('payment_method', 'bank_transfer')->count(),
            ]
        ];
        
        return $stats;
    }
} 