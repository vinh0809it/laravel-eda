<?php

namespace Src\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Src\Application\Booking\Listeners\BookingCanceledListener;
use Src\Application\Booking\Listeners\BookingChangedListener;
use Src\Application\Booking\Listeners\BookingCompletedListener;
use Src\Application\Booking\Listeners\BookingCreatedListener;
use Src\Application\Car\Listeners\PopularityFeeRecalculatedListener;
use Src\Application\Car\Listeners\RecalcFeeOnBookingCompletedListener;
use Src\Domain\Booking\Events\BookingCanceled;
use Src\Domain\Booking\Events\BookingChanged;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Car\Events\PopularityFeeRecalculated;
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
        BookingCompleted::class => [
            BookingCompletedListener::class, 
            RecalcFeeOnBookingCompletedListener::class,
            [BookingProjection::class, 'onBookingCompleted'],
            [CarProjection::class, 'onBookingCompleted'],
        ],
        BookingChanged::class => [
            BookingChangedListener::class, 
            [BookingProjection::class, 'onBookingChanged'],
            [CarProjection::class, 'onBookingChanged'],
        ],
        BookingCanceled::class => [
            BookingCanceledListener::class, 
            [BookingProjection::class, 'onBookingCanceled'],
            [CarProjection::class, 'onBookingCanceled'],
        ],
        PopularityFeeRecalculated::class => [
            PopularityFeeRecalculatedListener::class, 
            [CarProjection::class, 'onPopularityFeeRecalculated']
        ]
    ];

    public function boot(): void
    {
        parent::boot();
    }
} 