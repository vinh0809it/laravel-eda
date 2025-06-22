<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Commands;

use Carbon\Carbon;
use Src\Application\Booking\UseCases\Commands\CompleteBookingCommand;
use Src\Application\Booking\UseCases\Commands\CompleteBookingCommandHandler;
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

    // Mock dependencies
    $this->eventSourcingService = mock(IEventSourcingService::class);
    $this->bookingService = mock(IBookingService::class);
    $this->carService = mock(ICarService::class);
    $this->priceService = mock(IPriceService::class);
    
    // Create handler
    $this->handler = new CompleteBookingCommandHandler(
        $this->eventSourcingService,
        $this->bookingService,
        $this->carService,
        $this->priceService
    );

    // Command test data
    $this->command = new CompleteBookingCommand(
        bookingId: $this->bookingId
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

    // Mock car entity
    $this->car = new CarSnapshot(
        id: $this->carId,
        brand: faker()->word(),
        model: faker()->word(),
        year: (int) faker()->year(),
        pricePerDay: fakeMoney(),
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
        ->shouldReceive('getDailyPrice')
        ->with($this->carId)
        ->andReturn($dailyPrice);
    
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
    expect($result['id'])->toBeString();
    expect($result['actual_end_date'])->toBe(Carbon::now()->toDateString());
    expect($result['final_price'])->toBe($finalPrice);
    expect($result['status'])->toBe(BookingStatus::COMPLETED->value);
})
->group('complete_booking_handler');

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
->group('complete_booking_handler');