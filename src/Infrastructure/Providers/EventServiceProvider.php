<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Src\Application\Booking\Listeners\BookingCreatedListener;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Infrastructure\Booking\Projections\BookingProjection;
use Src\Infrastructure\Car\Projections\CarProjection;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            BookingCreatedListener::class, 
            [BookingProjection::class, 'onBookingCreated'],
            [CarProjection::class, 'onBookingCreated'],
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
} 