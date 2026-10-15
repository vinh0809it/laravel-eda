<?php

declare(strict_types=1);

namespace Src\Pricing\Infrastructure\Providers;

use Src\Pricing\Domain\Calculators\SimplePriceCalculator;
use Src\Pricing\Domain\Services\PriceCalculator;
use Src\Pricing\Contracts\PriceService;
use Src\Pricing\Domain\Services\DefaultPriceService;
use Src\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class PricingServiceProvider extends ModuleServiceProvider
{
    public array $bindings = [
        PriceCalculator::class => SimplePriceCalculator::class,
    ];

    public array $singletons = [
        PriceService::class => DefaultPriceService::class,
    ];
}
