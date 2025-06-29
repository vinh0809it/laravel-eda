<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Car\Services;

use Src\Domain\Car\Services\CarService;
use Src\Domain\Car\Exceptions\CarNotFoundException;
use Src\Domain\Car\ReadRepositories\ICarReadRepository;
use Src\Domain\Car\Snapshots\CarSnapshot;

beforeEach(function () {
    $this->readRepo = mock(ICarReadRepository::class);
    $this->service = new CarService($this->readRepo);
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
        'popularity_fee' => fakeMoney(),
        'booked_count' => 0
    ];

    $this->readRepo
        ->shouldReceive('findCarById')
        ->with($carId)
        ->andReturn(CarSnapshot::fromArray($carData));

    // Act
    $result = $this->service->findCarById($carId);

    // Assert
    expect($result)
        ->toBeInstanceOf(CarSnapshot::class)
        ->and($result->id)->toBe($carId)
        ->and($result->brand)->toBe($carData['brand'])
        ->and($result->model)->toBe($carData['model'])
        ->and($result->year)->toBe($carData['year'])
        ->and($result->pricePerDay)->toBe($carData['price_per_day'])
        ->and($result->popularityFee)->toBe($carData['popularity_fee']);
})
->group('car_service');

test('returns null when car not found', function () {
    // Arrange
    $carId = faker()->uuid();

    $this->readRepo
        ->shouldReceive('findCarById')
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
    $carId = fakeUuid();
    $pricePerDay = fakeMoney();

    $carData = [
        'id' => $carId,
        'brand' => faker()->word(),
        'model' => faker()->word(),
        'year' => (int) faker()->year(),
        'price_per_day' => $pricePerDay,
        'popularity_fee' => fakeMoney(),
        'booked_count' => faker()->randomNumber()
    ];

    $this->readRepo
        ->shouldReceive('findCarById')
        ->with($carId)
        ->andReturn(CarSnapshot::fromArray($carData));

    // Act
    $result = $this->service->getDailyPrice($carId);

    // Assert
    expect($result)->toBe($pricePerDay);
})
->group('car_service');

test('throws exception when getting price of non-existent car', function () {
    // Arrange
    $carId = faker()->uuid();

    $this->readRepo
        ->shouldReceive('findCarById')
        ->with($carId)
        ->andReturnNull();

    // Assert & Act
    expect(fn () => $this->service->getDailyPrice($carId))
        ->toThrow(CarNotFoundException::class);
})
->group('car_service'); 