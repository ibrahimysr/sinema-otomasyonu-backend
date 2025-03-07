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
        $cinemas = Cinema::all();
        
        if ($cinemas->isEmpty()) {
            $this->command->info('Önce sinema verilerini ekleyin!');
            return;
        }
        
        //CinemaHall::truncate();
        
        foreach ($cinemas as $cinema) {
            $hallCount = rand(2, 4);
            
            $hallTypes = ['2D', '3D', 'IMAX', 'VIP', '4DX', 'DBOX'];
            
            for ($i = 1; $i <= $hallCount; $i++) {
                $randomType = $hallTypes[array_rand($hallTypes)];
                
                $capacity = $this->getCapacityByType($randomType);
                
                $hallName = "Salon " . $i;
                
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
        $capacityOptions = [50, 60, 70];
        
        switch ($type) {
            case 'IMAX':
                return 70;
            case '3D':
                return $capacityOptions[array_rand(array_slice($capacityOptions, 1, 2))];
            case 'VIP':
                return 50;
            case '4DX':
                return 60;
            case 'DBOX':
                return $capacityOptions[array_rand(array_slice($capacityOptions, 0, 2))];
            case '2D':
            default:
                return $capacityOptions[array_rand($capacityOptions)];
        }
    }
} 