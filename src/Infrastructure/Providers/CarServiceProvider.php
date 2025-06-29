<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Car\ReadRepositories\ICarReadRepository;
use Src\Infrastructure\Car\Projections\CarProjection;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Car\Services\CarService;
use Src\Infrastructure\Car\ReadRepositories\CarReadRepository;

class CarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Projection bindings
        $this->app->singleton(ICarProjection::class, CarProjection::class);
        // Read Repo bindings
        $this->app->singleton(ICarReadRepository::class, CarReadRepository::class);
        // Service bindings
        $this->app->singleton(ICarService::class, CarService::class);
    }

    public function boot(): void
    {
        //
    }
} 