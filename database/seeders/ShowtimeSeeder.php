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
use Illuminate\Support\Facades\DB;

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

        // Foreign key kontrollerini geçici olarak devre dışı bırak
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Mevcut seansları temizle
        Showtime::query()->delete();
        
        // Foreign key kontrollerini tekrar etkinleştir
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($halls as $hall) {
            // Salonun koltuk verilerini al
            $seatData = $hall->seats ? $hall->seats->seat_data : null;
            
            // Eğer seat_data null ise, varsayılan bir koltuk düzeni oluştur
            if (!$seatData) {
                $this->command->info("Salon {$hall->id} ({$hall->name}) için koltuklar oluşturuluyor...");
                $seatData = $this->generateSeatData($hall);
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
    
    /**
     * Salon kapasitesine göre varsayılan bir koltuk düzeni oluştur
     * 
     * @param CinemaHall $hall
     * @return array
     */
    private function generateSeatData(CinemaHall $hall): array
    {
        
        $capacity = $hall->capacity ?: 50; 
        $rows = ceil(sqrt($capacity)); 
        
        $seatData = ['seats' => []];
        $seatCount = 0;
        
        for ($i = 0; $i < $rows && $seatCount < $capacity; $i++) {
            $rowName = chr(65 + $i); // A, B, C, ...
            $seatData['seats'][$rowName] = [];
            
            for ($j = 1; $j <= $rows && $seatCount < $capacity; $j++) {
                $seatData['seats'][$rowName][] = [
                    'id' => $rowName . $j,
                    'row' => $rowName,
                    'number' => $j,
                    'status' => 'available',
                    'type' => 'normal',
                    'price' => 0,
                ];
                $seatCount++;
            }
        }
        
        return $seatData;
    }
}
