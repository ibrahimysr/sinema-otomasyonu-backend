<?php

namespace Database\Seeders;

use App\Models\Showtime;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kullanıcı ve seans verilerini al
        $users = User::all();
        $showtimes = Showtime::all();
        
        if ($users->isEmpty() || $showtimes->isEmpty()) {
            $this->command->info('Kullanıcı veya seans bulunamadı. Önce bunları oluşturun.');
            return;
        }
        
        // Foreign key kontrollerini geçici olarak devre dışı bırak
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Mevcut biletleri temizle
        Ticket::query()->delete();
        
        // Foreign key kontrollerini tekrar etkinleştir
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // TicketService'i al
        $ticketService = app(TicketService::class);
        
        // Her seans için rastgele biletler oluştur
        foreach ($showtimes as $showtime) {
            // Seans koltuk durumunu al
            $seatStatus = json_decode($showtime->seat_status, true) ?: [];
            
            // Müsait koltukları bul
            $availableSeats = [];
            foreach ($seatStatus as $seatNumber => $status) {
                if ($status === 'available') {
                    $availableSeats[] = $seatNumber;
                }
            }
            
            // Eğer müsait koltuk yoksa, atla
            if (empty($availableSeats)) {
                continue;
            }
            
            // Rastgele 1-3 bilet oluştur
            $ticketCount = min(rand(1, 3), count($availableSeats));
            
            for ($i = 0; $i < $ticketCount; $i++) {
                // Rastgele bir kullanıcı seç
                $user = $users->random();
                
                // Rastgele bir koltuk seç
                $seatIndex = array_rand($availableSeats);
                $seatNumber = $availableSeats[$seatIndex];
                
                // Seçilen koltuğu listeden çıkar
                unset($availableSeats[$seatIndex]);
                $availableSeats = array_values($availableSeats);
                
                // Rastgele bir durum seç (çoğunlukla confirmed)
                $status = rand(1, 10) <= 8 ? 'confirmed' : (rand(0, 1) ? 'reserved' : 'cancelled');
                
                // Bilet oluştur
                $ticketData = [
                    'user_id' => $user->id,
                    'showtime_id' => $showtime->id,
                    'seat_number' => $seatNumber,
                    'price' => $showtime->price,
                    'status' => $status,
                    'ticket_code' => strtoupper(Str::random(8)),
                ];
                
                $ticket = $ticketService->createTicket($ticketData);
                
                $this->command->info("Bilet oluşturuldu: Seans #{$showtime->id}, Koltuk: {$seatNumber}, Kullanıcı: {$user->name}");
            }
        }
        
        $this->command->info('Biletler başarıyla eklendi!');
    }
}
