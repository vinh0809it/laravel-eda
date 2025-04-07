<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Car\Repositories\ICarRepository;
use Src\Infrastructure\Car\Repositories\CarRepository;
use Src\Infrastructure\Car\Models\Car;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Car\Services\CarService;

class CarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(ICarRepository::class, function ($app) {
            return new CarRepository($app->make(Car::class));
        });

        // Service bindings
        $this->app->singleton(ICarService::class, CarService::class);
    }

    public function boot(): void
    {
        //
    }
} 