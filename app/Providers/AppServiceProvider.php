<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
