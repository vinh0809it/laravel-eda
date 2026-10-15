<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\UseCases;

use Carbon\Carbon;
use Src\Booking\Application\UseCases\CancelBooking\CancelBookingCommand;
use Src\Booking\Application\UseCases\CancelBooking\CancelBookingHandler;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Domain\Aggregates\BookingAggregate;
use Src\Booking\Domain\Exceptions\BookingAccessDeniedException;
use Src\Booking\Domain\Enums\BookingStatus;
use Src\Booking\Domain\Events\BookingCreated;
use Src\Booking\Application\DTOs\CarDetails;
use Src\Shared\Application\Ports\AggregateRepository;

beforeEach(function () {
    // Mock variables
    $this->bookingId = fakeUuid();
    $this->carId = fakeUuid();
    $this->cancelReason = faker()->sentence();

    // Mock dependencies
    $this->eventSourcingService = mock(AggregateRepository::class);
    $this->bookingService = mock(BookingReadRepository::class);
    $this->carService = mock(CarCatalog::class);
    $this->priceService = mock(BookingPricing::class);
    
    // Create handler
    $this->handler = new CancelBookingHandler($this->eventSourcingService);

    // Command test data
    $this->userId = fakeUuid();

    $this->command = new CancelBookingCommand(
        bookingId: $this->bookingId,
        userId: $this->userId,
        cancelReason: $this->cancelReason
    );

    // Prepare Booking Created Event
    $this->events = [
        new BookingCreated(
            bookingId: $this->bookingId,
            carId: $this->carId,
            userId: $this->userId,
            startDate: fakeDateFromNow(),
            endDate: fakeDateFromNow(),
            originalPrice: fakeMoney(),
        ),
    ];

    // Mock car entity
    $this->car = new CarDetails(
        id: $this->carId,
        pricePerDay: fakeMoney(),
        popularityFee: fakeMoney(),
        bookedCount: 0
    );
});

test('successfully cancel a booking', function () {
    // Arrange

    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('save')
        ->once();
    
    // Act
    $result = $this->handler->handle($this->command);

    // Assert
    expect($result->id)->toBeString();
    expect($result->canceledAt->toDateTimeString())->toBe(Carbon::now()->toDateTimeString());
    expect($result->cancelReason)->toBe($this->cancelReason);
    expect($result->status)->toBe(BookingStatus::CANCELED->value);
})
->group('cancel_booking_handler');

test('throws exception when booking belongs to another user', function () {
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    $this->eventSourcingService->shouldNotReceive('save');

    $command = new CancelBookingCommand(
        bookingId: $this->bookingId,
        userId: fakeUuid(),
        cancelReason: $this->cancelReason
    );

    expect(fn () => $this->handler->handle($command))
        ->toThrow(BookingAccessDeniedException::class);
})
->group('cancel_booking_handler');
