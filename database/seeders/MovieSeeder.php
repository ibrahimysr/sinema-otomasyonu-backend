<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\MovieService;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $movieService = app(MovieService::class);
        
        $this->command->info('OMDB API\'den popüler filmler içe aktarılıyor...');
        
        $result = $movieService->importPopularMovies(20);
        
        if ($result['success']) {
            $this->command->info(sprintf('%d popüler film başarıyla içe aktarıldı.', $result['imported']));
        } else {
            $this->command->error('Filmler içe aktarılamadı.');
        }
    }
}