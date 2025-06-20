<?php

namespace Src\Infrastructure\Car\Projections;

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
        $loggerContext = self::class . '::onBookingCreated';

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->updateAvailability($event->carId, false);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    public function onBookingCompleted(BookingCompleted $event): void
    {
        $loggerContext = self::class . '::onBookingCompleted';

        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $this->updateAvailability($event->carId, true);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    public function updateAvailability(string $id, bool $isAvailable): void
    {
        $this->model->where('id', $id)->update(['is_available' => $isAvailable]);
    }
}