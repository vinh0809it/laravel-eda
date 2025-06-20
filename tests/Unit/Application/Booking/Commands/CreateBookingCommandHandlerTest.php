<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Commands;

use Illuminate\Support\Facades\Event;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommand;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommandHandler;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Domain\Car\Exceptions\CarNotAvailableException;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Booking\Exceptions\BookingConflictException;
use Src\Application\Car\DTOs\CarProjectionDTO;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Shared\Services\IEventSourcingService;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();

    // Mock dependencies
    $this->eventSourcingService = mock(IEventSourcingService::class);
    $this->bookingService = mock(IBookingService::class);
    $this->carService = mock(ICarService::class);
    $this->priceService = mock(IPriceService::class);
    
    // Create handler
    $this->handler = new CreateBookingCommandHandler(
        $this->eventSourcingService,
        $this->bookingService,
        $this->carService,
        $this->priceService
    );

    // Common test data
    $this->command = new CreateBookingCommand(
        userId: $this->faker->uuid(),
        carId: $this->faker->uuid(),
        startDate: $this->faker->date(),
        endDate: $this->faker->date()
    );

    // Mock car entity
    $this->carDTO = new CarProjectionDTO(
        id: $this->command->carId,
        brand: $this->faker->word(),
        model: $this->faker->word(),
        year: (int) $this->faker->year(),
        pricePerDay: $this->faker->randomFloat(2, 100, 1000),
        isAvailable: true
    );
});

test('successfully creates a booking when all conditions are met', function () {
    // Arrange
    $originalPrice = $this->faker->randomFloat(2, 100, 1000);
    
    // Mock car service to return car
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->command->carId)
        ->andReturn($this->carDTO);
    
    // Mock booking service to return no conflicts
    $this->bookingService
        ->shouldReceive('isConflictWithOtherBookings')
        ->with($this->command->userId, $this->command->startDate, $this->command->endDate)
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

    expect($result['id'])->toBeString();
    expect($result['car_id'])->toBe($this->command->carId);
    expect($result['user_id'])->toBe($this->command->userId);
    expect($result['start_date'])->toBe($this->command->startDate);
    expect($result['end_date'])->toBe($this->command->endDate);
    expect($result['original_price'])->toBe($originalPrice);
    expect($result['status'])->toBe(BookingStatus::CREATED->value);
})
->group('create_booking_handler');

test('throws exception when car is not found', function () {
    // Arrange
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->command->carId)
        ->andReturnNull();
        
    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(CarNotFoundException::class);
})
->group('create_booking_handler');

test('throws exception when car is not available', function () {
    // Arrange
    $unavailableCar = new CarProjectionDTO(
        id: $this->command->carId,
        brand: $this->faker->word(),
        model: $this->faker->word(),
        year: (int) $this->faker->year(),
        pricePerDay: $this->faker->randomFloat(2, 100, 1000),
        isAvailable: false
    );
    
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->command->carId)
        ->andReturn($unavailableCar);

    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(CarNotAvailableException::class);
})
->group('create_booking_handler');

test('throws exception when booking dates conflict', function () {
    // Arrange
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->command->carId)
        ->andReturn($this->carDTO);
    
    $this->bookingService
        ->shouldReceive('isConflictWithOtherBookings')
        ->with($this->command->userId, $this->command->startDate, $this->command->endDate)
        ->andReturnTrue();
      
    // Assert & Act
    expect(fn () => $this->handler->handle($this->command))
        ->toThrow(BookingConflictException::class);
})
->group('create_booking_handler');

test('validates command type', function () {
    // Arrange
    $invalidCommand = new class() implements ICommand {};
    
    // Assert & Act
    expect(fn () => $this->handler->handle($invalidCommand))
        ->toThrow(\InvalidArgumentException::class, 'Command must be an instance of CreateBookingCommand');
})
->group('create_booking_handler');
