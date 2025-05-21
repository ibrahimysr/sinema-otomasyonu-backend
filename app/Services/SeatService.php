<?php

namespace App\Services;

use App\Models\Seat;
use App\Repositories\SeatRepository;
use Illuminate\Database\Eloquent\Collection;

class SeatService
{
    protected $seatRepository;

    public function __construct(SeatRepository $seatRepository)
    {
        $this->seatRepository = $seatRepository;
    }

    /**
     * Tüm koltukları getir
     *
     * @return Collection
     */
    public function getAllSeats(): Collection
    {
        return $this->seatRepository->getAll();
    }

    /**
     * ID'ye göre koltuk bul
     *
     * @param int $id
     * @return Seat|null
     */
    public function getSeatById(int $id): ?Seat
    {
        return $this->seatRepository->findById($id);
    }

    /**
     * Salon ID'sine göre koltukları getir
     *
     * @param int $hallId
     * @return Seat|null
     */
    public function getSeatByCinemaHall(int $hallId): ?Seat
    {
        return $this->seatRepository->getByCinemaHallId($hallId);
    }

    /**
     * Yeni koltuk oluştur
     *
     * @param array $data
     * @return Seat
     */
    public function createSeat(array $data): Seat
    {
        // Koltuk verisi oluştur
        if (!isset($data['seat_data'])) {
            $data['seat_data'] = $this->generateSeatData($data['cinema_hall_id']);
        }
        
        return $this->seatRepository->create($data);
    }

    /**
     * Koltuk güncelle
     *
     * @param int $id
     * @param array $data
     * @return Seat|null
     */
    public function updateSeat(int $id, array $data): ?Seat
    {
        return $this->seatRepository->update($id, $data);
    }

    /**
     * Koltuk sil
     *
     * @param int $id
     * @return bool
     */
    public function deleteSeat(int $id): bool
    {
        return $this->seatRepository->delete($id);
    }

    /**
     * Salon kapasitesine göre koltuk verisi oluştur
     *
     * @param int $hallId
     * @return array
     */
    private function generateSeatData(int $hallId): array
    {
        // Salon bilgisini al
        $hall = app(\App\Models\CinemaHall::class)->find($hallId);
        
        if (!$hall) {
            return [];
        }
        
        $capacity = $hall->capacity;
        $seatData = [];
        
        // Salon tipine göre koltuk düzeni oluştur
        switch ($hall->type) {
            case 'IMAX':
            case '3D':
                // Daha geniş salonlar için (örn: 10 sıra, her sırada 5-7 koltuk)
                $rows = 10;
                break;
            case 'VIP':
                // VIP salonlar için daha az koltuk (örn: 5 sıra, her sırada 5-6 koltuk)
                $rows = 5;
                break;
            case '4DX':
            case 'DBOX':
                // Özel salonlar için orta düzey (örn: 7 sıra, her sırada 5-6 koltuk)
                $rows = 7;
                break;
            default:
                // Standart salonlar için (örn: 8 sıra, her sırada 5-7 koltuk)
                $rows = 8;
                break;
        }
        
        // Sıra başına ortalama koltuk sayısını hesapla
        $seatsPerRow = ceil($capacity / $rows);
        
        // Koltuk harfleri (sıra isimleri)
        $rowLetters = range('A', 'Z');
        
        // Her sıra için koltukları oluştur
        for ($i = 0; $i < $rows; $i++) {
            $rowName = $rowLetters[$i];
            $rowSeats = [];
            
            // Bu sıradaki koltuk sayısını belirle (biraz rastgelelik ekle)
            $currentRowSeats = min($seatsPerRow + rand(-1, 1), ceil($capacity / $rows) + 1);
            
            // Her koltuk için veri oluştur
            for ($j = 1; $j <= $currentRowSeats; $j++) {
                $seatNumber = $j;
                $seatId = $rowName . $seatNumber;
                
                $rowSeats[] = [
                    'id' => $seatId,
                    'row' => $rowName,
                    'number' => $seatNumber,
                    'status' => 'available', // Başlangıçta tüm koltuklar müsait
                    'type' => $this->getSeatType($i, $j, $currentRowSeats, $hall->type),
                    'price' => $this->getSeatPrice($i, $j, $currentRowSeats, $hall->type),
                ];
            }
            
            $seatData[$rowName] = $rowSeats;
        }
        
        return [
            'layout' => [
                'rows' => $rows,
                'seatsPerRow' => $seatsPerRow,
            ],
            'seats' => $seatData,
        ];
    }
    
    /**
     * Koltuğun tipini belirle (normal, vip, engelli vb.)
     *
     * @param int $row
     * @param int $seat
     * @param int $seatsInRow
     * @param string $hallType
     * @return string
     */
    private function getSeatType(int $row, int $seat, int $seatsInRow, string $hallType): string
    {
        // VIP salon ise tüm koltuklar VIP
        if ($hallType === 'VIP') {
            return 'vip';
        }
        
        // Orta sıralardaki orta koltuklar premium
        $middleRow = ($row >= 2 && $row <= 6);
        $middleSeat = ($seat > floor($seatsInRow * 0.3) && $seat < ceil($seatsInRow * 0.7));
        
        if ($middleRow && $middleSeat) {
            return 'premium';
        }
        
        // Her sıranın en sağ ve en sol koltukları (1. ve son) engelli koltukları olabilir
        // Sadece ilk ve son sıralarda, ilk ve son koltuklarda
        if (($row == 0 || $row == 9) && ($seat == 1 || $seat == $seatsInRow)) {
            // %10 ihtimalle engelli koltuğu
            return (rand(1, 10) == 1) ? 'disabled' : 'standard';
        }
        
        return 'standard';
    }
    
    /**
     * Koltuğun fiyatını belirle
     *
     * @param int $row
     * @param int $seat
     * @param int $seatsInRow
     * @param string $hallType
     * @return float
     */
    private function getSeatPrice(int $row, int $seat, int $seatsInRow, string $hallType): float
    {
        $basePrice = 50.0; 
        
        switch ($hallType) {
            case 'IMAX':
                $basePrice += 30.0;
                break;
            case '3D':
                $basePrice += 20.0;
                break;
            case 'VIP':
                $basePrice += 50.0;
                break;
            case '4DX':
                $basePrice += 40.0;
                break;
            case 'DBOX':
                $basePrice += 35.0;
                break;
        }
        
        // Koltuk tipine göre fiyat ayarlaması
        $seatType = $this->getSeatType($row, $seat, $seatsInRow, $hallType);
        switch ($seatType) {
            case 'vip':
                $basePrice += 25.0;
                break;
            case 'premium':
                $basePrice += 15.0;
                break;
            case 'disabled':
                $basePrice -= 10.0; // İndirimli
                break;
        }
        
        return round($basePrice, 2);
    }
} 