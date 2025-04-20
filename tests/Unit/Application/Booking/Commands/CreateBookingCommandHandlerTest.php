<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Booking\Commands;

use Illuminate\Support\Facades\Event;
use Src\Application\Booking\UseCases\Commands\CreateBookingCommand;
use Src\Application\Booking\UseCases\Commands\CreateBookingHandler;
use Src\Domain\Booking\Services\IBookingService;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Pricing\Services\IPriceService;
use Src\Domain\Shared\Repositories\IEventStoreRepository;
use Src\Domain\Car\Exceptions\CarNotAvailableException;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Booking\Exceptions\BookingConflictException;
use Src\Application\Car\DTOs\CarProjectionDTO;
use Src\Application\Shared\Interfaces\ICommand;
use Src\Domain\Booking\Events\BookingCreated;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    Event::fake();

    // Mock dependencies
    $this->eventStore = mock(IEventStoreRepository::class);
    $this->bookingService = mock(IBookingService::class);
    $this->carService = mock(ICarService::class);
    $this->priceService = mock(IPriceService::class);
    
    // Create handler
    $this->handler = new CreateBookingHandler(
        $this->eventStore,
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
    $totalPrice = $this->faker->randomFloat(2, 100, 1000);
    
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
        ->andReturn($totalPrice);
    
    // Mock event store
    $this->eventStore
        ->shouldReceive('append')
        ->once();
    
    // Act
    $result = $this->handler->handle($this->command);
    
    // Assert
    expect($result->getId())->toBeString();
    expect($result->getCarId())->toBe($this->command->carId);
    expect($result->getUserId())->toBe($this->command->userId);
    expect($result->getStartDate())->toBe($this->command->startDate);
    expect($result->getEndDate())->toBe($this->command->endDate);
    expect($result->getTotalPrice())->toBe($totalPrice);

    Event::assertDispatched(BookingCreated::class);
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

test('persists booking event to event store', function () {
    // Arrange
    $totalPrice = $this->faker->randomFloat(2, 100, 1000);
    
    $this->carService
        ->shouldReceive('findCarById')
        ->with($this->command->carId)
        ->andReturn($this->carDTO);
    
    $this->bookingService
        ->shouldReceive('isConflictWithOtherBookings')
        ->andReturnFalse();
    
    $this->priceService
        ->shouldReceive('calculateBookingPrice')
        ->andReturn($totalPrice);
    
    // Assert event store is called
    $this->eventStore
        ->shouldReceive('append')
        ->once()
        ->withArgs(function (
            string $eventType,
            string $aggregateType,
            string $aggregateId,
            array $eventData,
            array $metadata,
            int $version
        ) {
            expect($eventType)->toBe('BookingCreated');
            expect($aggregateType)->toBe('Booking');
            expect($aggregateId)->not->toBeEmpty();
            expect($eventData)->not->toBeEmpty();
            expect($version)->toBe(1);
    
            return true;
        });
    
    // Act
    $this->handler->handle($this->command);
})
->group('create_booking_handler'); 