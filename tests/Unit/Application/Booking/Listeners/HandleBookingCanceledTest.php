<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Application\Booking\Listeners\BookingCanceledListener;
use Src\Domain\Booking\Events\BookingCanceled;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->listener = new BookingCanceledListener();
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
    Log::shouldReceive('info')
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