<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Booking\Application\Listeners\BookingCompletedListener;
use Src\Booking\Domain\Events\BookingCompleted;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->logger = mock(LoggerInterface::class);
    $this->listener = new BookingCompletedListener($this->logger);
});

test('listener logs booking completion event', function () {
    // Arrange
    $event = new BookingCompleted(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        actualEndDate: fakeDateFromNow(),
        additionalPrice: fakeMoney(),
        finalPrice: fakeMoney(),
        completionNote: faker()->sentence()
    );

    // Assert log will be called
    $this->logger->shouldReceive('info')
        ->once()
        ->with('Booking completed', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'actual_end_date' => $event->actualEndDate,
            'additional_price' => $event->additionalPrice,
            'final_price' => $event->finalPrice
        ]);

    // Act
    $this->listener->handle($event);
})
->group('booking_event_listener'); 