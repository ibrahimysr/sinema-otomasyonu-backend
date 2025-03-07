<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
   
    public function register(): void
    {
        $this->app->bind(\App\Repositories\CinemaRepository::class, function ($app) {
            return new \App\Repositories\CinemaRepository($app->make(\App\Models\Cinema::class));
        });
        
        $this->app->bind(\App\Services\CinemaService::class, function ($app) {
            return new \App\Services\CinemaService(
                $app->make(\App\Repositories\CinemaRepository::class)
            );
        });
        
        $this->app->bind(\App\Repositories\CinemaHallRepository::class, function ($app) {
            return new \App\Repositories\CinemaHallRepository($app->make(\App\Models\CinemaHall::class));
        });
        
        $this->app->bind(\App\Services\CinemaHallService::class, function ($app) {
            return new \App\Services\CinemaHallService(
                $app->make(\App\Repositories\CinemaHallRepository::class)
            );
        });
        
        $this->app->bind(\App\Repositories\SeatRepository::class, function ($app) {
            return new \App\Repositories\SeatRepository($app->make(\App\Models\Seat::class));
        });
        
        $this->app->bind(\App\Services\SeatService::class, function ($app) {
            return new \App\Services\SeatService(
                $app->make(\App\Repositories\SeatRepository::class)
            );
        });
        
        $this->app->singleton(\App\Services\ResponseService::class, function ($app) {
            return new \App\Services\ResponseService();
        });
        
        $this->app->singleton(\App\Services\AuthService::class, function ($app) {
            return new \App\Services\AuthService();
        });
        
        $this->app->singleton(\App\Services\RoleService::class, function ($app) {
            return new \App\Services\RoleService();
        });
    }

    
    public function boot(): void
    {
        //
    }
}
