<?php

namespace Database\Seeders;

use App\Models\CinemaHall;
use App\Models\Movie;
use App\Models\Seat;
use App\Models\Showtime;
use App\Services\SeatService;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movieIds = Movie::pluck('id')->toArray();
        $halls = CinemaHall::with('seats')->get();

        if (empty($movieIds) || $halls->isEmpty()) {
            $this->command->info('Film veya salon bulunamadı. Önce bunları oluşturun.');
            return;
        }

        Showtime::truncate();

        foreach ($halls as $hall) {
            // Salonun koltuk verilerini al
            $seatData = $hall->seats ? $hall->seats->seat_data : null;
            
            if (!$seatData) {
                $this->command->info("Salon {$hall->id} için koltuk verisi bulunamadı, atlanıyor.");
                continue;
            }
            
            // Bugün için bir seans
            $this->createShowtime(
                $movieIds[array_rand($movieIds)],
                $hall->id,
                Carbon::now()->addHours(rand(1, 5)),
                $seatData
            );
            
            // Yarın için bir seans
            $this->createShowtime(
                $movieIds[array_rand($movieIds)],
                $hall->id,
                Carbon::tomorrow()->setHour(10 + rand(0, 8))->setMinute(0),
                $seatData
            );
        }

        $this->command->info('Seanslar başarıyla eklendi.');
    }
    
    /**
     * Yeni bir seans oluştur
     * 
     * @param int $movieId
     * @param int $hallId
     * @param Carbon $startTime
     * @param array $seatData
     * @return Showtime
     */
    private function createShowtime(int $movieId, int $hallId, Carbon $startTime, array $seatData): Showtime
    {
        $movie = Movie::find($movieId);
        $duration = $movie ? $movie->duration : 120;
        
        $endTime = (clone $startTime)->addMinutes($duration);
        
        $seatStatus = [];
        
        if (isset($seatData['seats'])) {
            foreach ($seatData['seats'] as $row => $seats) {
                foreach ($seats as $seat) {
                    // Rastgele koltuk durumu ata (çoğunlukla available)
                    $status = rand(1, 10) <= 8 ? 'available' : (rand(0, 1) ? 'reserved' : 'sold');
                    $seatStatus[$seat['id']] = $status;
                }
            }
        }
        
        $availableSeats = count(array_filter($seatStatus, function($status) {
            return $status === 'available';
        }));
        
        $price = rand(65, 95);
        
        return Showtime::create([
            'movie_id' => $movieId,
            'cinema_hall_id' => $hallId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $price,
            'available_seats' => $availableSeats,
            'seat_status' => json_encode($seatStatus),
        ]);
    }
}
