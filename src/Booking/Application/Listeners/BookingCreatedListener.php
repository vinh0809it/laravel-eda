<?php

declare(strict_types=1);

namespace Src\Booking\Application\Listeners;

use Src\Booking\Domain\Events\BookingCreated;
use Psr\Log\LoggerInterface;

final class BookingCreatedListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function handle(BookingCreated $event): void
    {
        $this->logger->info('Booking created', [
            'booking_id' => $event->bookingId,
            'user_id' => $event->userId,
            'car_id' => $event->carId,
            'original_price' => $event->originalPrice,
        ]);
    }
} 