<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Application\Booking\Listeners\BookingChangedListener;
use Src\Domain\Booking\Events\BookingChanged;

beforeEach(function () {
    $this->listener = new BookingChangedListener();
});

test('listener logs booking changing event', function () {
    // Arrange
    $event = new BookingChanged(
        bookingId: fakeUuid(),
        newStartDate: fakeDateFromNow(),
        newEndDate: fakeDateFromNow(2),
        newOriginalPrice: fakeMoney()
    );

    // Assert log will be called
    Log::shouldReceive('info')
        ->once()
        ->with('Booking changed', [
            'booking_id' => $event->bookingId,
            'start_date' => $event->newStartDate,
            'end_date' => $event->newEndDate,
            'original_price' => $event->newOriginalPrice
        ]);

    // Act
    $this->listener->handle($event);
})
->group('booking_event_listener'); 