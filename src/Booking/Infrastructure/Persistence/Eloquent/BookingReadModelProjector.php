<?php

declare(strict_types=1);

namespace Src\Booking\Infrastructure\Persistence\Eloquent;

use Illuminate\Contracts\Events\Dispatcher;
use Src\Booking\Domain\Enums\BookingStatus;
use Src\Booking\Domain\Events\BookingCanceled;
use Src\Booking\Domain\Events\BookingChanged;
use Src\Booking\Domain\Events\BookingCompleted;
use Src\Booking\Domain\Events\BookingCreated;
use Src\Shared\Application\Ports\EventProcessLogger;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentProjector;

/** Keeps the `bookings` read model in sync with the Booking aggregate's events. */
final class BookingReadModelProjector extends EloquentProjector
{
    public function __construct(
        BookingReadModel $model,
        private readonly EventProcessLogger $log
    ) {
        parent::__construct($model);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            BookingCreated::class => 'onBookingCreated',
            BookingChanged::class => 'onBookingChanged',
            BookingCompleted::class => 'onBookingCompleted',
            BookingCanceled::class => 'onBookingCanceled',
        ];
    }

    public function onBookingCreated(BookingCreated $event): void
    {
        $context = $this->context(__FUNCTION__);

        if ($this->log->hasProcessed($event->bookingId, $context)) {
            return;
        }

        $this->query()->create([
            'id' => $event->bookingId,
            'user_id' => $event->userId,
            'car_id' => $event->carId,
            'start_date' => $event->startDate,
            'end_date' => $event->endDate,
            'original_price' => $event->originalPrice,
            'status' => BookingStatus::CREATED->value,
        ]);

        $this->log->markSuccess($event->bookingId, $context);
    }

    public function onBookingChanged(BookingChanged $event): void
    {
        $this->apply($event->bookingId, __FUNCTION__, [
            'start_date' => $event->newStartDate,
            'end_date' => $event->newEndDate,
            'original_price' => $event->newOriginalPrice,
            'status' => BookingStatus::CHANGED->value,
        ]);
    }

    public function onBookingCompleted(BookingCompleted $event): void
    {
        $this->apply($event->bookingId, __FUNCTION__, [
            'actual_end_date' => $event->actualEndDate,
            'final_price' => $event->finalPrice,
            'completion_note' => $event->completionNote,
            'status' => BookingStatus::COMPLETED->value,
        ]);
    }

    public function onBookingCanceled(BookingCanceled $event): void
    {
        $this->apply($event->bookingId, __FUNCTION__, [
            'canceled_at' => $event->canceledAt,
            'cancel_reason' => $event->cancelReason,
            'status' => BookingStatus::CANCELED->value,
        ]);
    }

    /** @param array<string, mixed> $attributes */
    private function apply(string $bookingId, string $handler, array $attributes): void
    {
        $context = $this->context($handler);

        if ($this->log->hasProcessed($bookingId, $context)) {
            return;
        }

        $updated = $this->query()->whereKey($bookingId)->update($attributes);

        $updated === 0
            ? $this->log->markFailure($bookingId, $context, 'Booking not found in read model')
            : $this->log->markSuccess($bookingId, $context);
    }
}
