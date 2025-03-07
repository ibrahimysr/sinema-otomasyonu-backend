<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cinema;
use App\Models\CinemaHall;

class CinemaHallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tüm sinemaları al
        $cinemas = Cinema::all();
        
        if ($cinemas->isEmpty()) {
            $this->command->info('Önce sinema verilerini ekleyin!');
            return;
        }
        
        // Mevcut salonları temizle
        CinemaHall::truncate();
        
        // Her sinema için 2-4 salon ekle
        foreach ($cinemas as $cinema) {
            // Salon sayısını rastgele belirle (2-4 arası)
            $hallCount = rand(2, 4);
            
            // Salon tipleri
            $hallTypes = ['2D', '3D', 'IMAX', 'VIP', '4DX', 'DBOX'];
            
            for ($i = 1; $i <= $hallCount; $i++) {
                // Rastgele bir salon tipi seç
                $randomType = $hallTypes[array_rand($hallTypes)];
                
                // Kapasiteyi salon tipine göre belirle (minimum 50, maksimum 70, 10'un katları)
                $capacity = $this->getCapacityByType($randomType);
                
                // Salon adını oluştur
                $hallName = "Salon " . $i;
                
                // Salonu oluştur
                CinemaHall::create([
                    'cinema_id' => $cinema->id,
                    'name' => $hallName,
                    'capacity' => $capacity,
                    'type' => $randomType,
                ]);
            }
        }
        
        $this->command->info('Sinema salonları başarıyla eklendi!');
    }
    
    /**
     * Salon tipine göre kapasite belirle (minimum 50, maksimum 70, 10'un katları)
     *
     * @param string $type
     * @return int
     */
    private function getCapacityByType(string $type): int
    {
        // Kapasite değerleri (minimum 50, maksimum 70, 10'un katları)
        $capacityOptions = [50, 60, 70];
        
        switch ($type) {
            case 'IMAX':
                // IMAX için en büyük kapasite (70)
                return 70;
            case '3D':
                // 3D için büyük kapasite (60-70 arası)
                return $capacityOptions[array_rand(array_slice($capacityOptions, 1, 2))];
            case 'VIP':
                // VIP için minimum kapasite (50)
                return 50;
            case '4DX':
                // 4DX için orta-büyük kapasite (60)
                return 60;
            case 'DBOX':
                // DBOX için orta kapasite (50-60 arası)
                return $capacityOptions[array_rand(array_slice($capacityOptions, 0, 2))];
            case '2D':
            default:
                // 2D için genel kapasiteler (50-70 arası)
                return $capacityOptions[array_rand($capacityOptions)];
        }
    }
} 