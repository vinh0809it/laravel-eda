<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Car\Services;

use Src\Domain\Car\Services\CarService;
use Src\Domain\Car\Projections\ICarProjection;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Car\Snapshots\CarSnapshot;
use Src\Infrastructure\Car\Models\Car;

beforeEach(function () {
    $this->projection = mock(ICarProjection::class);
    $this->service = new CarService($this->projection);
});

test('finds car by id and returns Snapshot', function () {
    // Arrange
    $carId = faker()->uuid();
    $carData = [
        'id' => $carId,
        'brand' => faker()->word(),
        'model' => faker()->word(),
        'year' => (int) faker()->year(),
        'price_per_day' => fakeMoney(),
        'booked_count' => 0
    ];

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturn(new Car($carData));

    // Act
    $result = $this->service->findCarById($carId);

    // Assert
    expect($result)
        ->toBeInstanceOf(CarSnapshot::class)
        ->and($result->id)->toBe($carId)
        ->and($result->brand)->toBe($carData['brand'])
        ->and($result->model)->toBe($carData['model'])
        ->and($result->year)->toBe($carData['year'])
        ->and($result->pricePerDay)->toBe($carData['price_per_day']);
})
->group('car_service');

test('returns null when car not found', function () {
    // Arrange
    $carId = faker()->uuid();

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

test('gets daily price for car', function () {
    // Arrange
    $carId = faker()->uuid();
    $pricePerDay = faker()->randomFloat(2, 100, 1000);
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
    $carId = faker()->uuid();

    $this->projection
        ->shouldReceive('findById')
        ->with($carId)
        ->andReturnNull();

    // Assert & Act
    expect(fn () => $this->service->getDailyPrice($carId))
        ->toThrow(CarNotFoundException::class);
})
->group('car_service'); 