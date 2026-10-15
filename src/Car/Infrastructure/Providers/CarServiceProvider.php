<?php

declare(strict_types=1);

namespace Src\Car\Infrastructure\Providers;

use Src\Booking\Domain\Events\BookingCompleted;
use Src\Car\Application\Listeners\PopularityFeeRecalculatedListener;
use Src\Car\Application\Listeners\RecalcFeeOnBookingCompletedListener;
use Src\Car\Domain\Events\PopularityFeeRecalculated;
use Src\Car\Application\Ports\CarReadRepository;
use Src\Car\Application\Ports\PopularityFeeCalculator;
use Src\Car\Application\Services\ReadModelCarCatalog;
use Src\Car\Contracts\CarCatalog;
use Src\Car\Infrastructure\Adapters\PricingAdapter;
use Src\Car\Infrastructure\Persistence\Eloquent\CarProjector;
use Src\Car\Infrastructure\Persistence\Eloquent\EloquentCarReadRepository;
use Src\Shared\Infrastructure\Providers\ModuleServiceProvider;

final class CarServiceProvider extends ModuleServiceProvider
{
    public array $singletons = [
        CarReadRepository::class => EloquentCarReadRepository::class,
        CarCatalog::class => ReadModelCarCatalog::class,
        PopularityFeeCalculator::class => PricingAdapter::class,
    ];

    /** Side effects on Booking's events; the read model itself is kept by CarProjector. */
    protected array $listeners = [
        BookingCompleted::class => [RecalcFeeOnBookingCompletedListener::class],
        PopularityFeeRecalculated::class => [PopularityFeeRecalculatedListener::class],
    ];

    protected array $subscribers = [
        CarProjector::class,
    ];

    protected ?string $migrations = __DIR__ . '/../Migrations';
}
