<?php

declare(strict_types=1);

namespace Tests\Booking\Unit\Application\UseCases;

use Src\Booking\Application\UseCases\CreateBooking\CreateBookingCommand;
use Src\Booking\Application\UseCases\CreateBooking\CreateBookingHandler;
use Src\Booking\Application\Ports\BookingReadRepository;
use Src\Booking\Application\Ports\CarCatalog;
use Src\Booking\Application\Ports\BookingPricing;
use Src\Booking\Domain\Exceptions\CarNotFoundException;
use Src\Booking\Domain\Exceptions\BookingConflictException;
use Src\Shared\Application\Bus\Command;
use Src\Booking\Domain\Enums\BookingStatus;
use Src\Booking\Application\DTOs\CarDetails;
use Src\Shared\Application\Ports\AggregateRepository;

beforeEach(function () {

    // Mock dependencies
    $this->eventSourcingService = mock(AggregateRepository::class);
    $this->bookingService = mock(BookingReadRepository::class);
    $this->carService = mock(CarCatalog::class);
    $this->priceService = mock(BookingPricing::class);
    
    // Create handler
    $this->handler = new CreateBookingHandler(
        $this->eventSourcingService,
        $this->bookingService,
        $this->carService,
        $this->priceService
    );

    // Common test data
    $this->command = new CreateBookingCommand(
        userId: fakeUuid(),
        carId: fakeUuid(),
        startDate: fakeDateFromNow(),
        endDate: fakeDateFromNow()
    );

    // Mock car entity
    $this->car = new CarDetails(
        id: $this->command->carId,
        pricePerDay: fakeMoney(),
        popularityFee: fakeMoney(),
        bookedCount: 0
    );
});

test('successfully creates a booking when all conditions are met', function () {
    // Arrange
    $originalPrice = fakeMoney();
    
    // Mock car service to return car
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->command->carId)
        ->andReturn($this->car);
    
    // Mock booking service to return no conflicts
    $this->bookingService
        ->shouldReceive('hasBookingConflict')
        ->with($this->command->userId, $this->command->carId, $this->command->startDate, $this->command->endDate)
        ->andReturnFalse();
    
    // Mock price calculation
    $this->priceService
        ->shouldReceive('calculateBookingPrice')
        ->andReturn($originalPrice);
    
    // Mock event sourcing service
    $this->eventSourcingService
        ->shouldReceive('save')
        ->once();
    
    // Act
    $result = $this->handler->handle($this->command);
    // Assert

    expect($result->id)->toBeString();
    expect($result->carId)->toBe($this->command->carId);
    expect($result->userId)->toBe($this->command->userId);
    expect($result->startDate->toDateString())->toBe($this->command->startDate->toDateString());
    expect($result->endDate->toDateString())->toBe($this->command->endDate->toDateString());
    expect($result->originalPrice)->toBe($originalPrice);
    expect($result->status)->toBe(BookingStatus::CREATED->value);
})
->group('create_booking_handler');

test('throws exception when car is not found', function () {
    // Arrange
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->car->id)
        ->andReturnNull();
       
    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(CarNotFoundException::class);
})
->group('create_booking_handler');

test('throws exception when booking dates conflict', function () {
    // Arrange
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->command->carId)
        ->andReturn($this->car);
    
    $this->bookingService
        ->shouldReceive('hasBookingConflict')
        ->with($this->command->userId, $this->command->carId, $this->command->startDate, $this->command->endDate)
        ->andReturnTrue();
      
    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(BookingConflictException::class);
})
->group('create_booking_handler');

test('validates command type', function () {
    // Arrange
    $invalidCommand = new class() implements Command {};
    
    // Assert & Act
    expect(fn () => $this->handler->handle($invalidCommand))
        ->toThrow(\InvalidArgumentException::class, 'Command must be an instance of CreateBookingCommand');
})
->group('create_booking_handler');
