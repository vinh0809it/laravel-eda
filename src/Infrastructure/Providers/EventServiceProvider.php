<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Src\Application\Booking\Listeners\HandleBookingCreated;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Application\Booking\Projections\BookingProjection;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            HandleBookingCreated::class, 
            [BookingProjection::class, 'onBookingCreated']
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
} 