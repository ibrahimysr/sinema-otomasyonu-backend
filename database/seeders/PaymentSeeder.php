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
        $tickets = Ticket::where('status', 'confirmed')->get();
        
        if ($tickets->isEmpty()) {
            $this->command->info('Onaylanmış bilet bulunamadı. Önce biletleri oluşturun ve onaylayın.');
            return;
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Payment::query()->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $paymentService = app(PaymentService::class);
        
        
        $paymentMethods = ['credit_card', 'cash', 'bank_transfer', 'online'];
        
        foreach ($tickets as $ticket) {
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            $paymentData = [
                'user_id' => $ticket->user_id,
                'ticket_id' => $ticket->id,
                'amount' => $ticket->price,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
            ];
            
            $payment = $paymentService->createPayment($paymentData);
            
            $this->command->info("Ödeme oluşturuldu: Bilet #{$ticket->id}, Tutar: {$payment->amount}, Yöntem: {$payment->payment_method}");
        }
        
        $pendingTickets = Ticket::where('status', 'reserved')->take(5)->get();
        
        foreach ($pendingTickets as $ticket) {
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            $paymentData = [
                'user_id' => $ticket->user_id,
                'ticket_id' => $ticket->id,
                'amount' => $ticket->price,
                'payment_method' => $paymentMethod,
                'status' => rand(0, 1) ? 'pending' : 'failed',
            ];
            
            $payment = Payment::create($paymentData);
            
            $this->command->info("Ödeme oluşturuldu: Bilet #{$ticket->id}, Tutar: {$payment->amount}, Durum: {$payment->status}");
        }
        
        $this->command->info('Ödemeler başarıyla eklendi!');
    }
}
