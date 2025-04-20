<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Car\Services;

use Src\Domain\Car\Services\CarService;
use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Application\Car\DTOs\CarProjectionDTO;
use Src\Infrastructure\Car\Models\Car;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->projection = mock(ICarProjection::class);
    $this->service = new CarService($this->projection);
});

test('finds car by id and returns DTO', function () {
    // Arrange
    $carId = $this->faker->uuid();
    $carData = [
        'id' => $carId,
        'brand' => $this->faker->word(),
        'model' => $this->faker->word(),
        'year' => (int) $this->faker->year(),
        'price_per_day' => $this->faker->randomFloat(2, 100, 1000),
        'is_available' => true
    ];

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturn(new Car($carData));

    // Act
    $result = $this->service->findCarById($carId);

    // Assert
    expect($result)
        ->toBeInstanceOf(CarProjectionDTO::class)
        ->and($result->id)->toBe($carId)
        ->and($result->brand)->toBe($carData['brand'])
        ->and($result->model)->toBe($carData['model'])
        ->and($result->year)->toBe($carData['year'])
        ->and($result->pricePerDay)->toBe($carData['price_per_day'])
        ->and($result->isAvailable)->toBe($carData['is_available']);
})
->group('car_service');

test('returns null when car not found', function () {
    // Arrange
    $carId = $this->faker->uuid();

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturnNull();

    // Act
    $result = $this->service->findCarById($carId);

    // Assert
    expect($result)->toBeNull();
})
->group('car_service');

test('checks car availability', function () {
    // Arrange
    $carId = $this->faker->uuid();
    $carData = [
        'id' => $carId,
        'is_available' => true
    ];

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturn(new Car($carData));

    // Act
    $result = $this->service->isAvailable($carId);

    // Assert
    expect($result)->toBeTrue();
})
->group('car_service');

test('throws exception when checking availability of non-existent car', function () {
    // Arrange
    $carId = $this->faker->uuid();

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturnNull();

    // Assert & Act
    expect(fn () => $this->service->isAvailable($carId))
        ->toThrow(CarNotFoundException::class);
})
->group('car_service');

test('checks if car exists', function () {
    // Arrange
    $carId = $this->faker->uuid();

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturn(new Car(['id' => $carId]));

    // Act
    $result = $this->service->isCarExists($carId);

    // Assert
    expect($result)->toBeTrue();
})
->group('car_service');

test('returns false when car does not exist', function () {
    // Arrange
    $carId = $this->faker->uuid();

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturnNull();

    // Act
    $result = $this->service->isCarExists($carId);

    // Assert
    expect($result)->toBeFalse();
})
->group('car_service');

test('gets daily price for car', function () {
    // Arrange
    $carId = $this->faker->uuid();
    $pricePerDay = $this->faker->randomFloat(2, 100, 1000);
    $carData = [
        'id' => $carId,
        'price_per_day' => $pricePerDay
    ];

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturn(new Car($carData));

    // Act
    $result = $this->service->getDailyPrice($carId);

    // Assert
    expect($result)->toBe($pricePerDay);
})
->group('car_service');

test('throws exception when getting price of non-existent car', function () {
    // Arrange
    $carId = $this->faker->uuid();

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturnNull();

    // Assert & Act
    expect(fn () => $this->service->getDailyPrice($carId))
        ->toThrow(CarNotFoundException::class);
})
->group('car_service'); 