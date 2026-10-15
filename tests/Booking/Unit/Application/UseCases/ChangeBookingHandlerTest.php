<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\UseCases;

use Src\Booking\Application\UseCases\ChangeBooking\ChangeBookingCommand;
use Src\Booking\Application\UseCases\ChangeBooking\ChangeBookingHandler;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Domain\Exceptions\CarNotFoundException;
use Src\Booking\Domain\Aggregates\BookingAggregate;
use Src\Booking\Domain\Exceptions\BookingAccessDeniedException;
use Src\Booking\Domain\Exceptions\BookingConflictException;
use Src\Booking\Domain\Enums\BookingStatus;
use Src\Booking\Domain\Events\BookingCreated;
use Src\Booking\Application\DTOs\CarDetails;
use Src\Shared\Application\Ports\AggregateRepository;

beforeEach(function () {

    // Mock variables
    $this->bookingId = fakeUuid();
    $this->carId = fakeUuid();
    $this->bookingDays = 2;

    // Mock dependencies
    $this->eventSourcingService = mock(AggregateRepository::class);
    $this->bookingService = mock(BookingReadRepository::class);
    $this->carService = mock(CarCatalog::class);
    $this->priceService = mock(BookingPricing::class);
    
    // Create handler
    $this->handler = new ChangeBookingHandler(
        $this->eventSourcingService,
        $this->bookingService,
        $this->carService,
        $this->priceService
    );

    // Command data
    $this->userId = fakeUuid();

    $this->command = new ChangeBookingCommand(
        bookingId: $this->bookingId,
        userId: $this->userId,
        newStartDate: fakeDateFromNow(),
        newEndDate: fakeDateFromNow($this->bookingDays)
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

    // Mock car snapshot
    $this->car = new CarDetails(
        id: $this->carId,
        pricePerDay: fakeMoney(),
        popularityFee: fakeMoney(),
        bookedCount: 0
    );
});

test('successfully change a booking', function () {
    // Arrange
    $newOriginalPrice = fakeMoney();

    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    // Mock booking service to return no conflicts
    $this->bookingService
        ->shouldReceive('hasBookingConflict')
        ->with($this->userId, $this->carId, $this->command->newStartDate, $this->command->newEndDate, $this->bookingId)
        ->andReturnFalse();

    // Mock car service to return car
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->carId)
        ->andReturn($this->car);
    
    // Mock price calculation
    $this->priceService
        ->shouldReceive('calculateBookingPrice')
        ->with($this->car->pricePerDay, $this->car->popularityFee, $this->command->newStartDate, $this->command->newEndDate)
        ->andReturn($newOriginalPrice);
    
    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('save')
        ->once();
    
    // Act
    $result = $this->handler->handle($this->command);

    // Assert
    expect($result->id)->toBeString();
    expect($result->startDate->toDateString())->toBe($this->command->newStartDate->toDateString());
    expect($result->endDate->toDateString())->toBe($this->command->newEndDate->toDateString());
    expect($result->originalPrice)->toBe($newOriginalPrice);
    expect($result->status)->toBe(BookingStatus::CHANGED->value);
})
->group('change_booking_handler');

test('throws exception when car is not found', function () {
    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    $this->bookingService
        ->shouldReceive('hasBookingConflict')
        ->andReturnFalse();

    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->carId)
        ->andReturnNull();

    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(CarNotFoundException::class);
})
->group('change_booking_handler');

test('throws exception when booking belongs to another user', function () {
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    $this->eventSourcingService->shouldNotReceive('save');

    $command = new ChangeBookingCommand(
        bookingId: $this->bookingId,
        userId: fakeUuid(),
        newStartDate: fakeDateFromNow(),
        newEndDate: fakeDateFromNow($this->bookingDays)
    );

    expect(fn () => $this->handler->handle($command))
        ->toThrow(BookingAccessDeniedException::class);
})
->group('change_booking_handler');

test('throws exception when the new dates conflict with another booking', function () {
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    $this->bookingService
        ->shouldReceive('hasBookingConflict')
        ->with($this->userId, $this->carId, $this->command->newStartDate, $this->command->newEndDate, $this->bookingId)
        ->andReturnTrue();

    $this->eventSourcingService->shouldNotReceive('save');

    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(BookingConflictException::class);
})
->group('change_booking_handler');
