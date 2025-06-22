<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Listeners;

use Src\Domain\Booking\Events\BookingCreated;
use Src\Application\Booking\Listeners\BookingCreatedListener;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->listener = new BookingCreatedListener();
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
    Log::shouldReceive('info')
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