<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\Listeners;

use Src\Booking\Domain\Events\BookingCreated;
use Src\Booking\Application\Listeners\BookingCreatedListener;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->listener = new BookingCreatedListener($this->logger);
});

test('listener logs booking creation event', function () {
    // Arrange
    $event = new BookingCreated(
        bookingId: fakeUuid(),
        userId: fakeUuid(),
        carId: fakeUuid(),
        startDate: fakeDateFromNow(),
        endDate: fakeDateFromNow(),
        originalPrice: fakeMoney()
    );

    // Assert log will be called
    $this->logger->shouldReceive('info')
        ->once()
        ->with('Booking created', [
            'booking_id' => $event->bookingId,
            'user_id' => $event->userId,
            'car_id' => $event->carId,
            'original_price' => $event->originalPrice,
        ]);

    // Act
    $this->listener->handle($event);
})
->group('booking_event_listener'); 