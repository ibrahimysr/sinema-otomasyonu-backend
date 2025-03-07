<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CinemaHall;
use App\Models\Seat;
use App\Services\SeatService;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tüm salonları al
        $halls = CinemaHall::all();
        
        if ($halls->isEmpty()) {
            $this->command->info('Önce salon verilerini ekleyin!');
            return;
        }
        
        // Mevcut koltukları temizle
        Seat::truncate();
        
        // SeatService'i al
        $seatService = app(SeatService::class);
        
        // Her salon için koltuk verisi oluştur
        foreach ($halls as $hall) {
            $this->command->info("Salon {$hall->id} ({$hall->name}) için koltuklar oluşturuluyor...");
            
            // Koltuk verisi oluştur
            $seatService->createSeat([
                'cinema_hall_id' => $hall->id,
                'status' => 'active',
            ]);
        }
        
        $this->command->info('Koltuklar başarıyla eklendi!');
    }
} 