<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Commands;

use Carbon\Carbon;
use Src\Application\Booking\UseCases\Commands\CancelBookingCommand;
use Src\Application\Booking\UseCases\Commands\CancelBookingCommandHandler;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Car\Snapshots\CarSnapshot;
use Src\Domain\Shared\Services\IEventSourcingService;

beforeEach(function () {
    // Mock variables
    $this->bookingId = fakeUuid();
    $this->carId = fakeUuid();
    $this->cancelReason = faker()->sentence();

    // Mock dependencies
    $this->eventSourcingService = mock(IEventSourcingService::class);
    $this->bookingService = mock(IBookingService::class);
    $this->carService = mock(ICarService::class);
    $this->priceService = mock(IPriceService::class);
    
    // Create handler
    $this->handler = new CancelBookingCommandHandler(
        $this->eventSourcingService,
        $this->bookingService,
        $this->carService,
        $this->priceService
    );

    // Command test data
    $this->command = new CancelBookingCommand(
        bookingId: $this->bookingId,
        cancelReason: $this->cancelReason
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
    expect($result['id'])->toBeString();
    expect($result['canceled_at'])->toBe(Carbon::now()->toDateTimeString());
    expect($result['cancel_reason'])->toBe($this->cancelReason);
    expect($result['status'])->toBe(BookingStatus::CANCELED->value);
})
->group('cancel_booking_handler');
