<?php

declare(strict_types=1);

namespace Src\Booking\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Booking\Domain\Events\BookingCompleted;

final class BookingCompletedListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function handle(BookingCompleted $event): void
    {
        $this->logger->info('Booking completed', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'actual_end_date' => $event->actualEndDate,
            'additional_price' => $event->additionalPrice,
            'final_price' => $event->finalPrice
        ]);
    }
} 