<?php

namespace Src\Infrastructure\Car\Projections;

use Carbon\Carbon;
use Src\Domain\Booking\Events\BookingChanged;
use Src\Infrastructure\Car\Models\Car;
use Src\Infrastructure\Shared\Projections\BaseProjection;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Shared\Loggers\IEventProcessLogger;

class CarProjection extends BaseProjection implements ICarProjection
{
    public function __construct(
        Car $model,
        private readonly IEventProcessLogger $logger
    ) {
        parent::__construct($model);
    }

    public function onBookingCreated(BookingCreated $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->increaseBookedCount($event->carId);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    public function onBookingCompleted(BookingCompleted $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->updateLastBookingCompletedAt($event->carId, $event->actualEndDate);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    public function onBookingChanged(BookingChanged $event): void
    {
        $loggerContext = $this->context(__FUNCTION__);

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        // TODO:
        // update something
        
        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    public function increaseBookedCount(string $id): void
    {
        $this->model->where('id', $id)->increment('booked_count');
    }

    public function updateLastBookingCompletedAt(string $id, Carbon $completedAt): void
    {
        $this->model->where('id', $id)->update([
            'last_booking_completed_at' => $completedAt
        ]);
    }
}