<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Car\Projections\ICarProjection;
use Src\Infrastructure\Car\Projections\CarProjection;
use Src\Infrastructure\Car\Models\Car;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Car\Services\CarService;

class CarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Projection bindings
        $this->app->singleton(ICarProjection::class, CarProjection::class);
        // Service bindings
        $this->app->singleton(ICarService::class, CarService::class);
    }

    public function boot(): void
    {
        //
    }
} 