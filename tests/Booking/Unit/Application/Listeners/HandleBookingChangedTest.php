<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Booking\Application\Listeners\BookingChangedListener;
use Src\Booking\Domain\Events\BookingChanged;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->listener = new BookingChangedListener($this->logger);
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
    $this->logger->shouldReceive('info')
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