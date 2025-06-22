<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Booking\Services;

use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\ReadRepositories\IBookingReadRepository;
use Src\Domain\Booking\Services\BookingService;
use Src\Domain\Booking\Snapshots\BookingSnapshot;

beforeEach(function () {
    $this->readRepo = mock(IBookingReadRepository::class);
    $this->service = new BookingService($this->readRepo);
});

test('BookingService detects conflicts when dates overlap', function () {
    // Arrange
    $userId = fakeUuid();
    $inputStart = fakeDateFromNow();
    $inputEnd = fakeDateFromNow(5);
    $existingStart = faker()->date($inputStart);
    $existingEnd = faker()->date($existingStart);

    // Mock existing bookings that overlap
    $this->readRepo->shouldReceive('findByDateRange')
        ->withArgs(function ($startDateArg, $endDateArg) use ($inputStart, $inputEnd) {
            return $startDateArg === $inputStart &&
                   $endDateArg === $inputEnd;
        })
        ->andReturn([
            BookingSnapshot::fromArray([
                'id' => fakeUuid(),
                'car_id' => fakeUuid(),
                'user_id' => $userId,
                'start_date' => $existingStart,
                'end_date' => $existingEnd,
                'original_price' => faker()->randomFLoat(2, 100, 1000),
                'status' => faker()->randomElement(BookingStatus::toArray()),
            ])
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
    $userId = fakeUuid();
    $inputStart = fakeDateFromNow();
    $inputEnd = $inputStart;

    // Mock existing bookings that don't overlap
    $this->readRepo->shouldReceive('findByDateRange')
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
