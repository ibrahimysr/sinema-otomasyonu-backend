<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CitySeeder::class);
        $this->call(CinemaSeeder::class);
        $this->call(CinemaHallSeeder::class);
        $this->call(SeatSeeder::class);
        $this->call(MovieSeeder::class);
        $this->call(ShowtimeSeeder::class);
        $this->call(TicketSeeder::class);
        $this->call(PaymentSeeder::class);
    }
}