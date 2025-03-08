<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Ticket;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bilet verilerini al
        $tickets = Ticket::where('status', 'confirmed')->get();
        
        if ($tickets->isEmpty()) {
            $this->command->info('Onaylanmış bilet bulunamadı. Önce biletleri oluşturun ve onaylayın.');
            return;
        }
        
        // Foreign key kontrollerini geçici olarak devre dışı bırak
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Mevcut ödemeleri temizle
        Payment::query()->delete();
        
        // Foreign key kontrollerini tekrar etkinleştir
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // PaymentService'i al
        $paymentService = app(PaymentService::class);
        
        // Ödeme yöntemleri
        $paymentMethods = ['credit_card', 'cash', 'bank_transfer', 'online'];
        
        // Her onaylanmış bilet için ödeme oluştur
        foreach ($tickets as $ticket) {
            // Rastgele bir ödeme yöntemi seç
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            // Ödeme verisi oluştur
            $paymentData = [
                'user_id' => $ticket->user_id,
                'ticket_id' => $ticket->id,
                'amount' => $ticket->price,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
            ];
            
            // Ödeme oluştur
            $payment = $paymentService->createPayment($paymentData);
            
            $this->command->info("Ödeme oluşturuldu: Bilet #{$ticket->id}, Tutar: {$payment->amount}, Yöntem: {$payment->payment_method}");
        }
        
        // Bazı biletler için başarısız ödemeler oluştur
        $pendingTickets = Ticket::where('status', 'reserved')->take(5)->get();
        
        foreach ($pendingTickets as $ticket) {
            // Rastgele bir ödeme yöntemi seç
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            // Ödeme verisi oluştur
            $paymentData = [
                'user_id' => $ticket->user_id,
                'ticket_id' => $ticket->id,
                'amount' => $ticket->price,
                'payment_method' => $paymentMethod,
                'status' => rand(0, 1) ? 'pending' : 'failed',
            ];
            
            // Ödeme oluştur
            $payment = Payment::create($paymentData);
            
            $this->command->info("Ödeme oluşturuldu: Bilet #{$ticket->id}, Tutar: {$payment->amount}, Durum: {$payment->status}");
        }
        
        $this->command->info('Ödemeler başarıyla eklendi!');
    }
}
