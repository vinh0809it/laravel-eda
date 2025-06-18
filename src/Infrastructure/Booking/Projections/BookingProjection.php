<?php

namespace Src\Infrastructure\Booking\Projections;

use Src\Infrastructure\Booking\Models\Booking;
use Src\Infrastructure\Shared\Projections\BaseProjection;
use Src\Domain\Booking\Projections\IBookingProjection;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Shared\Loggers\IEventProcessLogger;

class BookingProjection extends BaseProjection implements IBookingProjection
{
    public function __construct(
        Booking $model,
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

        $this->model->create([
            'id' => $event->bookingId,
            'user_id' => $event->userId,
            'car_id' => $event->carId,  
            'start_date' => $event->startDate,
            'end_date' => $event->endDate,
            'original_price' => $event->originalPrice,
            'status' => BookingStatus::CREATED,
        ]);

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }

    public function onBookingCompleted(BookingCompleted $event): void
    {
        $loggerContext = self::class . '::onBookingCompleted';
        if ($this->logger->hasProcessed($event->bookingId, $loggerContext)) {
            return;
        }

        $booking = $this->model->find($event->bookingId);

        if(!$booking) {
            $this->logger->markFailure($event->bookingId, $loggerContext, 'Booking not found for completion');
            return;
        }
        
        $booking->actual_end_date = $event->actualEndDate;
        $booking->final_price = $event->finalPrice;
        $booking->status = BookingStatus::COMPLETED->value;
        $booking->save();

        $this->logger->markSuccess($event->bookingId, $loggerContext);
    }
} 