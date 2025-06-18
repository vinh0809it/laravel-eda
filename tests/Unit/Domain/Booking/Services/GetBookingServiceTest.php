<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Booking\Services;

use Src\Domain\Booking\Services\BookingService;
use Src\Application\Shared\DTOs\PaginationDTO;
use Src\Application\Booking\DTOs\BookingDTO;
use Src\Domain\Booking\Exceptions\BookingNotFoundException;
use Src\Domain\Booking\ReadRepositories\IBookingReadRepository;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->readRepo = mock(IBookingReadRepository::class);
    $this->service = new BookingService($this->readRepo);
});

test('returns paginated bookings with filters', function () {
    // Arrange
    $page = 1;
    $perPage = 10;
    $sortBy = 'created_at';
    $sortDirection = 'desc';

    $filters = [
        'user_id' => $this->faker->uuid(),
        'car_id' => $this->faker->uuid(),
        'status' => 'created',
    ];

    $expectedBooking = new BookingDTO(
        id: $this->faker->uuid(),
        carId: $filters['car_id'],
        userId: $filters['user_id'],
        startDate: now()->format('Y-m-d H:i:s'),
        endDate: now()->addDays(5)->format('Y-m-d H:i:s'),
        originalPrice: 500.00,
        status: $filters['status']
    );

    $expectedPaginationResult = new PaginationDTO(
        items: [$expectedBooking],
        currentPage: $page,
        perPage: $perPage,
        total: 1,
        lastPage: 1
    );

    $this->readRepo
        ->shouldReceive('paginate')
        ->once()
        ->andReturn($expectedPaginationResult);

    // Act
    $actualResult = $this->service->getBookings(
        page: $page,
        perPage: $perPage,
        sortBy: $sortBy,
        sortDirection: $sortDirection,
        filters: $filters
    );

    // Assert
    expect($actualResult->items())->toBe([$expectedBooking]);
    expect($actualResult->currentPage())->toBe($page);
    expect($actualResult->perPage())->toBe($perPage);
    expect($actualResult->total())->toBe(1);
    expect($actualResult->lastPage())->toBe(1);
})
->group('booking_service');

test('returns booking by id', function () {
    // Arrange
    $bookingId = $this->faker->uuid();
    $expectedBooking = new BookingDTO(
        id: $bookingId,
        carId: $this->faker->uuid(),
        userId: $this->faker->uuid(),
        startDate: now()->format('Y-m-d H:i:s'),
        endDate: now()->addDays(5)->format('Y-m-d H:i:s'),
        originalPrice: 500.00,
        status: 'created'
    );

    $this->readRepo->shouldReceive('findById')
        ->with($bookingId)
        ->andReturn($expectedBooking);

    // Act
    $result = $this->service->getBookingById($bookingId);

    // Assert
    expect($result)->toBe($expectedBooking);
})
->group('booking_service');

test('returns null when booking not found', function () {
    // Arrange
    $bookingId = $this->faker->uuid();

    $this->readRepo->shouldReceive('findById')
        ->with($bookingId)
        ->andThrow(new BookingNotFoundException(trace: ['bookingId' => $bookingId]));

    $this->expectException(BookingNotFoundException::class);
        
    // Act
    $this->service->getBookingById($bookingId);
})
->group('booking_service');