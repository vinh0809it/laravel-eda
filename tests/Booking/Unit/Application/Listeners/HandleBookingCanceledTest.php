<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Booking\Application\Listeners\BookingCanceledListener;
use Src\Booking\Domain\Events\BookingCanceled;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->logger = mock(LoggerInterface::class);
    $this->listener = new BookingCanceledListener($this->logger);
});

test('listener logs booking cancelation event', function () {
    // Arrange
    $event = new BookingCanceled(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        canceledAt: now(),
        cancelReason: faker()->sentence()
    );

    // Assert log will be called
    $this->logger->shouldReceive('info')
        ->once()
        ->with('Booking completed', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'canceled_at' => $event->canceledAt,
            'cancel_reason' => $event->cancelReason
        ]);

    // Act
    $this->listener->handle($event);
})
->group('booking_event_listener'); 