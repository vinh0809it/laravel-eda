<?php

declare(strict_types=1);

namespace Src\Car\Infrastructure\Persistence\Eloquent;

use Illuminate\Contracts\Events\Dispatcher;
use Src\Booking\Domain\Events\BookingCanceled;
use Src\Booking\Domain\Events\BookingCompleted;
use Src\Booking\Domain\Events\BookingCreated;
use Src\Car\Domain\Events\PopularityFeeRecalculated;
use Src\Shared\Application\Ports\EventProcessLogger;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentProjector;

/**
 * Keeps the `cars` table's booking statistics (booked count, last completion,
 * popularity fee) in sync with Booking's events and Car's own events.
 */
final class CarProjector extends EloquentProjector
{
    public function __construct(
        CarModel $model,
        private readonly EventProcessLogger $log
    ) {
        parent::__construct($model);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            BookingCreated::class => 'onBookingCreated',
            BookingCompleted::class => 'onBookingCompleted',
            BookingCanceled::class => 'onBookingCanceled',
            PopularityFeeRecalculated::class => 'onPopularityFeeRecalculated',
        ];
    }

    public function onBookingCreated(BookingCreated $event): void
    {
        $this->once($event->bookingId, __FUNCTION__, fn () => $this->query()->whereKey($event->carId)->increment('booked_count'));
    }

    public function onBookingCompleted(BookingCompleted $event): void
    {
        $this->once($event->bookingId, __FUNCTION__, fn () => $this->query()->whereKey($event->carId)->update([
            'last_booking_completed_at' => $event->actualEndDate,
        ]));
    }

    public function onBookingCanceled(BookingCanceled $event): void
    {
        $this->once($event->bookingId, __FUNCTION__, fn () => $this->query()->whereKey($event->carId)->decrement('booked_count'));
    }

    public function onPopularityFeeRecalculated(PopularityFeeRecalculated $event): void
    {
        $this->once($event->bookingId, __FUNCTION__, fn () => $this->query()->whereKey($event->carId)->update([
            'popularity_fee' => $event->newPopularityFee,
        ]));
    }

    /** Idempotency guard: run the projection once per (event, handler). */
    private function once(string $eventKey, string $handler, callable $projection): void
    {
        $context = $this->context($handler);

        if ($this->log->hasProcessed($eventKey, $context)) {
            return;
        }

        $projection();

        $this->log->markSuccess($eventKey, $context);
    }
}
