<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Services\PaymentService;
use App\Services\TicketService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $ticketService;
    protected $responseService;

    /**
     * PaymentController constructor.
     *
     * @param PaymentService $paymentService
     * @param TicketService $ticketService
     * @param ResponseService $responseService
     */
    public function __construct(
        PaymentService $paymentService,
        TicketService $ticketService,
        ResponseService $responseService
    ) {
        $this->paymentService = $paymentService;
        $this->ticketService = $ticketService;
        $this->responseService = $responseService;
    }

    /**
     * Tüm ödemeleri listele
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $payments = $this->paymentService->getAllPayments();
        return $this->responseService->success($payments, 'Ödemeler başarıyla listelendi.');
    }

    /**
     * Belirli bir ödemeyi göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $payment = $this->paymentService->getPaymentById($id);
        
        if (!$payment) {
            return $this->responseService->notFound('Ödeme bulunamadı.');
        }
        
        return $this->responseService->success($payment, 'Ödeme başarıyla bulundu.');
    }

    /**
     * Kullanıcıya göre ödemeleri listele
     *
     * @param int $user_id
     * @return JsonResponse
     */
    public function byUser(int $user_id): JsonResponse
    {
        $payments = $this->paymentService->getPaymentsByUser($user_id);
        
        if ($payments->isEmpty()) {
            return $this->responseService->notFound('Bu kullanıcıya ait ödeme bulunamadı.');
        }
        
        return $this->responseService->success($payments, 'Kullanıcıya ait ödemeler başarıyla listelendi.');
    }

    /**
     * Bilete göre ödemeleri listele
     *
     * @param int $ticket_id
     * @return JsonResponse
     */
    public function byTicket(int $ticket_id): JsonResponse
    {
        $payments = $this->paymentService->getPaymentsByTicket($ticket_id);
        
        if ($payments->isEmpty()) {
            return $this->responseService->notFound('Bu bilete ait ödeme bulunamadı.');
        }
        
        return $this->responseService->success($payments, 'Bilete ait ödemeler başarıyla listelendi.');
    }

    /**
     * Duruma göre ödemeleri listele
     *
     * @param string $status
     * @return JsonResponse
     */
    public function byStatus(string $status): JsonResponse
    {
        $payments = $this->paymentService->getPaymentsByStatus($status);
        
        if ($payments->isEmpty()) {
            return $this->responseService->notFound('Bu durumda ödeme bulunamadı.');
        }
        
        return $this->responseService->success($payments, 'Duruma ait ödemeler başarıyla listelendi.');
    }

    /**
     * Yeni bir ödeme oluştur
     *
     * @param StorePaymentRequest $request
     * @return JsonResponse
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Bilet bilgisini al
        $ticket = $this->ticketService->getTicketById($data['ticket_id']);
        
        if (!$ticket) {
            return $this->responseService->notFound('Bilet bulunamadı.');
        }
        
        // Eğer bilet zaten onaylanmışsa, hata döndür
        if ($ticket->status === 'confirmed') {
            return $this->responseService->error('Bu bilet zaten onaylanmış.', 400);
        }
        
        // Eğer bilet iptal edilmişse, hata döndür
        if ($ticket->status === 'cancelled') {
            return $this->responseService->error('Bu bilet iptal edilmiş.', 400);
        }
        
        // Ödeme oluştur
        $payment = $this->paymentService->createPayment($data);
        
        return $this->responseService->success($payment, 'Ödeme başarıyla oluşturuldu.', 201);
    }

    /**
     * Mevcut bir ödemeyi güncelle
     *
     * @param UpdatePaymentRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdatePaymentRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        
        $payment = $this->paymentService->updatePayment($id, $data);
        
        if (!$payment) {
            return $this->responseService->notFound('Ödeme bulunamadı.');
        }
        
        return $this->responseService->success($payment, 'Ödeme başarıyla güncellendi.');
    }

    /**
     * Bir ödemeyi sil
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->paymentService->deletePayment($id);
        
        if (!$result) {
            return $this->responseService->notFound('Ödeme bulunamadı.');
        }
        
        return $this->responseService->success(null, 'Ödeme başarıyla silindi.');
    }
} 