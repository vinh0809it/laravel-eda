<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\UseCases;

use Carbon\Carbon;
use Src\Booking\Application\UseCases\CompleteBooking\CompleteBookingCommand;
use Src\Booking\Application\UseCases\CompleteBooking\CompleteBookingHandler;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Domain\Exceptions\CarNotFoundException;
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

    // Mock dependencies
    $this->eventSourcingService = mock(AggregateRepository::class);
    $this->bookingService = mock(BookingReadRepository::class);
    $this->carService = mock(CarCatalog::class);
    $this->priceService = mock(BookingPricing::class);
    
    // Create handler
    $this->handler = new CompleteBookingHandler(
        $this->eventSourcingService,
        $this->carService,
        $this->priceService
    );

    // Command test data
    $this->userId = fakeUuid();

    $this->command = new CompleteBookingCommand(
        bookingId: $this->bookingId,
        userId: $this->userId,
        completionNote: faker()->sentence()
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

test('successfully complete a booking', function () {
    // Arrange
    $dailyPrice = fakeMoney();
    $additionalPrice = fakeMoney();
    $finalPrice = fakeMoney();

    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    // Mock car service to return car
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->carId)
        ->andReturn($this->car);
    
    // Mock price calculation
    $this->priceService
        ->shouldReceive('calculateAdditionalPrice')
        ->withAnyArgs()
        ->andReturn($additionalPrice);
    
    $this->priceService
        ->shouldReceive('calculateFinalPrice')
        ->withAnyArgs()
        ->andReturn($finalPrice);

    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('save')
        ->once();
    
    // Act
    $result = $this->handler->handle($this->command);

    // Assert
    expect($result->id)->toBeString();
    expect($result->actualEndDate->toDateString())->toBe(Carbon::now()->toDateString());
    expect($result->finalPrice)->toBe($finalPrice);
    expect($result->status)->toBe(BookingStatus::COMPLETED->value);
})
->group('complete_booking_handler');

test('throws exception when car is not found', function () {
    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->carId)
        ->andReturnNull();
        
    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(CarNotFoundException::class);
})
->group('complete_booking_handler');

test('throws exception when booking belongs to another user', function () {
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    $this->eventSourcingService->shouldNotReceive('save');

    $command = new CompleteBookingCommand(
        bookingId: $this->bookingId,
        userId: fakeUuid(),
        completionNote: faker()->sentence()
    );

    expect(fn () => $this->handler->handle($command))
        ->toThrow(BookingAccessDeniedException::class);
})
->group('complete_booking_handler');
