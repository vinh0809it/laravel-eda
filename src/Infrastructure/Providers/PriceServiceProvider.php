<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Domain\Pricing\Services\PriceService;
use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Domain\Pricing\Calculators\SimplePriceCalculator;

class PriceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the price calculator
        $this->app->bind(IPriceCalculator::class, SimplePriceCalculator::class);

        // Bind the price service
        $this->app->singleton(IPriceService::class, PriceService::class);
    }

    public function boot(): void
    {
        //
    }
} 