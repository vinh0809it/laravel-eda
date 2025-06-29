<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Pricing\Services;

use Carbon\Carbon;
use Src\Domain\Pricing\Services\PriceService;
use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;
use Src\Domain\Car\Snapshots\CarSnapshot;

beforeEach(function () {
    $this->priceCalculator = mock(IPriceCalculator::class);
    $this->service = new PriceService($this->priceCalculator);
});

test('test calculates booking price correctly', function () {
    // Arrange
    $dailyPrice = fakeMoney();
    $popularityFee = fakeMoney();

    $startDate = fakeDateFromNow();
    $endDate = fakeDateFromNow(5);

    $expectedUsagePrice = $dailyPrice * 5;
    $expectedOriginalPrice = $expectedUsagePrice+ $popularityFee;

    $car = new CarSnapshot(
        id: fakeUuid(),
        brand: faker()->word(),
        model: faker()->word(),
        year: (int) faker()->year(),
        pricePerDay: $dailyPrice,
        popularityFee: $popularityFee,
        bookedCount: 0
    );

    $this->priceCalculator
        ->shouldReceive('calculateUsagePrice')
        ->once()
        ->andReturn($expectedUsagePrice);

    // Act
    $result = $this->service->calculateBookingPrice($car->pricePerDay, $car->popularityFee, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('price_service');

test('test handles single day booking', function () {
    // Arrange
    $dailyPrice = fakeMoney();
    $popularityFee = fakeMoney();

    $startDate = fakeDateFromNow();
    $endDate = fakeDateFromNow(1);

    $expectedUsagePrice = $dailyPrice;
    $expectedOriginalPrice = $expectedUsagePrice+ $popularityFee;

    $car = new CarSnapshot(
        id: fakeUuid(),
        brand: faker()->word(),
        model: faker()->word(),
        year: (int) faker()->year(),
        pricePerDay: $dailyPrice,
        popularityFee: $popularityFee,
        bookedCount: 0
    );

    $this->priceCalculator
        ->shouldReceive('calculateUsagePrice')
        ->once()
        ->andReturn($expectedUsagePrice);

    // Act
    $result = $this->service->calculateBookingPrice($car->pricePerDay, $car->popularityFee, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('price_service');

test('test calculate additional price', function () {
    // Arrange
    $dailyPrice = fakeMoney();

    $end = fakeDateFromNow();
    $actualEnd = fakeDateFromNow(2);

    $expectedAddtionalPrice = $dailyPrice * 2;

    $additionalPriceCalculationDTO = new AdditionalPriceCalculationDTO(
        pricePerDay: $dailyPrice,
        endDate: $end,
        actualEndDate: $actualEnd
    );

    $this->priceCalculator
        ->shouldReceive('calculateUsagePrice')
        ->once()
        ->andReturn($expectedAddtionalPrice);

    // Act
    $result = $this->service->calculateAdditionalPrice($additionalPriceCalculationDTO);

    // Assert
    expect($result)->toBe($expectedAddtionalPrice);
})
->group('price_service');

test('test no additional price when end date equal to actual end date', function () {
    // Arrange
    $dailyPrice = fakeMoney();

    $end = fakeDateFromNow();
    $actualEnd = fakeDateFromNow();

    $expectedAddtionalPrice = 0.0;

    $additionalPriceCalculationDTO = new AdditionalPriceCalculationDTO(
        pricePerDay: $dailyPrice,
        endDate: $end,
        actualEndDate: $actualEnd
    );

    $this->priceCalculator
        ->shouldReceive('calculateUsagePrice')
        ->once()
        ->andReturn($expectedAddtionalPrice);

    // Act
    $result = $this->service->calculateAdditionalPrice($additionalPriceCalculationDTO);

    // Assert
    expect($result)->toBe($expectedAddtionalPrice);
})
->group('price_service');

test('test calculate final price', function () {
    // Arrange
    $originalPrice = fakeMoney();
    $additionalPrice = fakeMoney();

    $expectedFinalPrice = $originalPrice + $additionalPrice;

    // Act
    $result = $this->service->calculateFinalPrice(
        $originalPrice,
        $additionalPrice
    );

    // Assert
    expect($result)->toBe($expectedFinalPrice);
})
->group('price_service');

test('test calculate popularity fee', function () {
    // Arrange
    $dailyPrice = fakeMoney();
    $bookedCount = faker()->randomNumber(2);
    $expectedPopularityFee = fakeMoney();

    $this->priceCalculator
        ->shouldReceive('calculatePopularityFee')
        ->once()
        ->andReturn($expectedPopularityFee);

    // Act
    $result = $this->service->calculatePopularityFee(
        $dailyPrice,
        $bookedCount
    );

    // Assert
    expect($result)->toBe($expectedPopularityFee);
})
->group('price_service');