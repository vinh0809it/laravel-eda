<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Booking\Services;

use Src\Domain\Booking\ReadRepositories\IBookingReadRepository;
use Src\Domain\Booking\Services\BookingService;

beforeEach(function () {
    $this->readRepo = mock(IBookingReadRepository::class);
    $this->service = new BookingService($this->readRepo);
});

test('BookingService detects conflicts when dates overlap', function () {
    // Arrange
    $userId = fakeUuid();
    $carId = fakeUuid();
    $inputStart = fakeDateFromNow();
    $inputEnd = fakeDateFromNow(5);

    // Mock existing bookings that overlap
    $this->readRepo->shouldReceive('hasBookingConflict')
        ->with($userId, $carId, $inputStart, $inputEnd)
        ->andReturn(true);

    // Act
    $hasConflict = $this->service->hasBookingConflict(
        userId: $userId,
        carId: $carId,
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
    $carId = fakeUuid();
    $inputStart = fakeDateFromNow();
    $inputEnd = $inputStart;

    // Mock existing bookings that don't overlap
    $this->readRepo->shouldReceive('hasBookingConflict')
        ->with($userId, $carId, $inputStart, $inputEnd)
        ->andReturn(false);

    // Act
    $hasConflict = $this->service->hasBookingConflict(
        userId: $userId,
        carId: $carId,
        startDate: $inputStart,
        endDate: $inputEnd
    );

    // Assert
    expect($hasConflict)->toBeFalse();
})
->group('create_booking_service');
