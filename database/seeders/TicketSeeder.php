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
        $users = User::all();
        $showtimes = Showtime::all();
        
        if ($users->isEmpty() || $showtimes->isEmpty()) {
            $this->command->info('Kullanıcı veya seans bulunamadı. Önce bunları oluşturun.');
            return;
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Ticket::query()->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $ticketService = app(TicketService::class);
        
        foreach ($showtimes as $showtime) {
            $seatStatus = json_decode($showtime->seat_status, true) ?: [];
            
            $availableSeats = [];
            foreach ($seatStatus as $seatNumber => $status) {
                if ($status === 'available') {
                    $availableSeats[] = $seatNumber;
                }
            }
            
            if (empty($availableSeats)) {
                continue;
            }
            
            $ticketCount = min(rand(1, 3), count($availableSeats));
            
            for ($i = 0; $i < $ticketCount; $i++) {
                $user = $users->random();
                
                $seatIndex = array_rand($availableSeats);
                $seatNumber = $availableSeats[$seatIndex];
                
                unset($availableSeats[$seatIndex]);
                $availableSeats = array_values($availableSeats);
                
                $status = rand(1, 10) <= 8 ? 'confirmed' : (rand(0, 1) ? 'reserved' : 'cancelled');
                
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
