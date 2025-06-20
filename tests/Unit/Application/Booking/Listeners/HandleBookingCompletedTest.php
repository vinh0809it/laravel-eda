<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Application\Booking\Listeners\BookingCompletedListener;
use Src\Domain\Booking\Events\BookingCompleted;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->listener = new BookingCompletedListener();
});

test('listener logs booking creation event', function () {
    // Arrange
    $event = new BookingCompleted(
        bookingId: $this->faker->uuid(),
        carId: $this->faker->uuid(),
        actualEndDate: $this->faker->date(),
        additionalPrice: $this->faker->randomFloat(2, 100, 1000),
        finalPrice: $this->faker->randomFloat(2, 100, 1000)
    );

    // Assert log will be called
    Log::shouldReceive('info')
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