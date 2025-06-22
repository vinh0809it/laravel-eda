<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Commands;

use Src\Application\Booking\UseCases\Commands\ChangeBookingCommand;
use Src\Application\Booking\UseCases\Commands\ChangeBookingCommandHandler;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Car\Snapshots\CarSnapshot;
use Src\Domain\Shared\Services\IEventSourcingService;

beforeEach(function () {

    // Mock variables
    $this->bookingId = fakeUuid();
    $this->carId = fakeUuid();
    $this->bookingDays = 2;

    // Mock dependencies
    $this->eventSourcingService = mock(IEventSourcingService::class);
    $this->bookingService = mock(IBookingService::class);
    $this->carService = mock(ICarService::class);
    $this->priceService = mock(IPriceService::class);
    
    // Create handler
    $this->handler = new ChangeBookingCommandHandler(
        $this->eventSourcingService,
        $this->bookingService,
        $this->carService,
        $this->priceService
    );

    // Command data
    $this->command = new ChangeBookingCommand(
        bookingId: $this->bookingId,
        newStartDate: fakeDateFromNow(),
        newEndDate: fakeDateFromNow($this->bookingDays)
    );

    // Prepare Booking Created Event
    $this->events = [
        new BookingCreated(
            bookingId: $this->bookingId,
            carId: $this->carId,
            userId: fakeUuid(),
            startDate: fakeDateFromNow(),
            endDate: fakeDateFromNow(),
            originalPrice: fakeMoney(),
        ),
    ];

    // Mock car snapshot
    $this->car = new CarSnapshot(
        id: $this->carId,
        brand: faker()->word(),
        model: faker()->word(),
        year: (int) faker()->year(),
        pricePerDay: fakeMoney(),
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

    // Mock car service to return car
    $this->carService
        ->shouldReceive('getDailyPrice')
        ->with($this->carId)
        ->andReturn($this->car->pricePerDay);
    
    // Mock price calculation
    $this->priceService
        ->shouldReceive('calculateBookingPrice')
        ->with($this->car->pricePerDay, $this->command->newStartDate, $this->command->newEndDate)
        ->andReturn($newOriginalPrice);
    
    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('save')
        ->once();
    
    // Act
    $result = $this->handler->handle($this->command);

    // Assert
    expect($result['id'])->toBeString();
    expect($result['start_date'])->toBe($this->command->newStartDate->toDateString());
    expect($result['end_date'])->toBe($this->command->newEndDate->toDateString());
    expect($result['original_price'])->toBe($newOriginalPrice);
    expect($result['status'])->toBe(BookingStatus::CHANGED->value);
})
->group('change_booking_handler');

test('throws exception when car is not found', function () {
    // Mock event store
    $this->eventSourcingService
        ->shouldReceive('getEvents')
        ->with(BookingAggregate::AGGREGATE_TYPE, $this->bookingId)
        ->andReturn($this->events);

    $this->carService
        ->shouldReceive('getDailyPrice')
        ->with($this->carId)
        ->andThrow(
            new CarNotFoundException(
                trace: ['carId' => $this->carId]
            )
        );

    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(CarNotFoundException::class);
})
->group('change_booking_handler');