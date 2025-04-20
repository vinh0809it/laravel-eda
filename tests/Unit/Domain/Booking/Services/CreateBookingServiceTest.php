<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Booking\Services;

use Src\Domain\Booking\Projections\IBookingProjection;
use Src\Domain\Booking\Services\BookingService;


beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->projection = mock(IBookingProjection::class);
    $this->service = new BookingService($this->projection);
});


test('BookingService detects conflicts when dates overlap', function () {
    // Arrange
    $userId = $this->faker->uuid();
    $inputStart = $this->faker->date();
    $inputEnd = $this->faker->date($inputStart);
    $existingStart = $this->faker->date($inputStart);
    $existingEnd = $this->faker->date($existingStart);

    // Mock existing bookings that overlap
    $this->projection->shouldReceive('findByDateRange')
        ->withArgs(function ($startDateArg, $endDateArg) use ($inputStart, $inputEnd) {
            return $startDateArg === $inputStart &&
                   $endDateArg === $inputEnd;
        })
        ->andReturn([
            [
                'user_id' => $userId,
                'start_date' => $existingStart,
                'end_date' => $existingEnd,
            ]
        ]);

    // Act
    $hasConflict = $this->service->isConflictWithOtherBookings(
        userId: $userId,
        startDate: $inputStart,
        endDate: $inputEnd
    );

    // Assert
    expect($hasConflict)->toBeTrue();
})
->group('create_booking_service');

test('BookingService returns no conflict when dates do not overlap', function () {
    // Arrange
    $userId = $this->faker->uuid();
    $inputStart = $this->faker->date();
    $inputEnd = $this->faker->date($inputStart);

    // Mock existing bookings that don't overlap
    $this->projection->shouldReceive('findByDateRange')
        ->withArgs(function ($startDateArg, $endDateArg) use ($inputStart, $inputEnd) {
            return $startDateArg === $inputStart &&
                   $endDateArg === $inputEnd;
        })
        ->andReturn([]);

    // Act
    $hasConflict = $this->service->isConflictWithOtherBookings(
        userId: $userId,
        startDate: $inputStart,
        endDate: $inputEnd
    );

    // Assert
    expect($hasConflict)->toBeFalse();
})
->group('create_booking_service');
