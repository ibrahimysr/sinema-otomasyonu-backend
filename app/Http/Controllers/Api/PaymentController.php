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
use Yajra\DataTables\Facades\DataTables;

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

    /**
     * DataTables için ödeme verilerini getir
     *
     * @param Request $request
     * @return mixed
     */
    public function datatable(Request $request)
    {
        $query = $this->paymentService->getPaymentsQuery();

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        return DataTables::of($query)
            ->addColumn('ticket_code', function ($payment) {
                if ($payment->ticket) {
                    return '<span class="badge bg-dark">
                        <i class="fas fa-barcode me-1"></i>' . $payment->ticket->ticket_code . '
                    </span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('customer', function ($payment) {
                if ($payment->user) {
                    return '<div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <span>' . $payment->user->name . '</span>
                    </div>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('amount', function ($payment) {
                return '<span class="badge bg-success">
                    <i class="fas fa-lira-sign me-1"></i>' . number_format($payment->amount, 2) . '
                </span>';
            })
            ->addColumn('payment_method', function ($payment) {
                $methodClass = 'bg-secondary';
                $methodIcon = 'fa-money-bill';
                $methodText = 'Bilinmiyor';
                
                if ($payment->payment_method == 'credit_card') {
                    $methodClass = 'bg-info';
                    $methodIcon = 'fa-credit-card';
                    $methodText = 'Kredi Kartı';
                } else if ($payment->payment_method == 'cash') {
                    $methodClass = 'bg-success';
                    $methodIcon = 'fa-money-bill';
                    $methodText = 'Nakit';
                } else if ($payment->payment_method == 'bank_transfer') {
                    $methodClass = 'bg-primary';
                    $methodIcon = 'fa-university';
                    $methodText = 'Banka Transferi';
                }
                
                return '<span class="badge ' . $methodClass . '">
                    <i class="fas ' . $methodIcon . ' me-1"></i>' . $methodText . '
                </span>';
            })
            ->addColumn('payment_date', function ($payment) {
                $date = new \DateTime($payment->created_at);
                return '<span class="badge bg-info">
                    <i class="fas fa-calendar-alt me-1"></i>' . $date->format('d.m.Y H:i') . '
                </span>';
            })
            ->addColumn('status', function ($payment) {
                $statusClass = 'bg-secondary';
                $statusIcon = 'fa-question-circle';
                $statusText = 'Bilinmiyor';
                
                if ($payment->status == 'completed') {
                    $statusClass = 'bg-success';
                    $statusIcon = 'fa-check-circle';
                    $statusText = 'Tamamlandı';
                } else if ($payment->status == 'pending') {
                    $statusClass = 'bg-warning';
                    $statusIcon = 'fa-clock';
                    $statusText = 'Beklemede';
                } else if ($payment->status == 'cancelled') {
                    $statusClass = 'bg-danger';
                    $statusIcon = 'fa-times-circle';
                    $statusText = 'İptal Edildi';
                }
                
                return '<span class="badge ' . $statusClass . '">
                    <i class="fas ' . $statusIcon . ' me-1"></i>' . $statusText . '
                </span>';
            })
            ->addColumn('actions', function ($payment) {
                return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info view-payment" data-id="' . $payment->id . '">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary edit-payment" data-id="' . $payment->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-payment" data-id="' . $payment->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['ticket_code', 'customer', 'amount', 'payment_method', 'payment_date', 'status', 'actions'])
            ->make(true);
    }
    
    /**
     * Ödeme istatistiklerini getir
     *
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->paymentService->getPaymentStats();
        return $this->responseService->success($stats, 'Ödeme istatistikleri başarıyla alındı.');
    }
} 