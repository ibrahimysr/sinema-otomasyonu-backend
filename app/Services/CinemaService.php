<?php

namespace App\Services;

use App\Repositories\CinemaRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Cinema;
use Illuminate\Support\Facades\DB;

class CinemaService
{
    protected $cinemaRepository;

    public function __construct(CinemaRepository $cinemaRepository)
    {
        $this->cinemaRepository = $cinemaRepository;
    }

    public function getAllCinemas(): Collection
    {
        return $this->cinemaRepository->getAll();
    }

    public function getCinemaById(int $id): ?Cinema
    {
        return $this->cinemaRepository->findById($id);
    }

    public function getCinemasByCity(int $cityId): Collection
    {
        return $this->cinemaRepository->getByCityId($cityId);
    }

    public function createCinema(array $data): Cinema
    {
        return $this->cinemaRepository->create($data);
    }

    public function updateCinema(int $id, array $data): ?Cinema
    {
        return $this->cinemaRepository->update($id, $data);
    }

    /**
     * Sinemayı sil
     * 
     * @param int $id
     * @return bool|array
     */
    public function deleteCinema(int $id)
    {
        // İlişkili kayıtları kontrol et
        $relatedRecords = $this->checkRelatedRecords($id);
        
        // İlişkili kayıt varsa, silme işlemini yapma ve ilişkili kayıtları döndür
        if (!empty($relatedRecords)) {
            return [
                'success' => false,
                'related_records' => $relatedRecords
            ];
        }
        
        // İlişkili kayıt yoksa, soft delete yap
        return $this->cinemaRepository->delete($id);
    }
    
    /**
     * Sinemaya bağlı ilişkili kayıtları kontrol et
     * 
     * @param int $id
     * @return array
     */
    protected function checkRelatedRecords(int $id): array
    {
        $relatedRecords = [];
        $cinema = $this->getCinemaById($id);
        
        if (!$cinema) {
            return $relatedRecords;
        }
        
        // İleride eklenecek ilişkili tablolar için kontrol
        // Örnek: Salonlar (Halls)
        // if (class_exists('App\Models\Hall') && $cinema->halls()->count() > 0) {
        //     $relatedRecords['halls'] = $cinema->halls()->count();
        // }
        
        // Örnek: Seanslar (Sessions)
        // if (class_exists('App\Models\Session') && $cinema->sessions()->count() > 0) {
        //     $relatedRecords['sessions'] = $cinema->sessions()->count();
        // }
        
        // Örnek: Biletler (Tickets)
        // if (class_exists('App\Models\Ticket') && $cinema->tickets()->count() > 0) {
        //     $relatedRecords['tickets'] = $cinema->tickets()->count();
        // }
        
        return $relatedRecords;
    }
} 