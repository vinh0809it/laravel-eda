<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Pricing\Services;

use Carbon\Carbon;
use Src\Domain\Pricing\Services\PriceService;
use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Application\Car\DTOs\CarProjectionDTO;
use Src\Application\Pricing\DTOs\AdditionalPriceCalculationDTO;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->priceCalculator = mock(IPriceCalculator::class);
    $this->service = new PriceService($this->priceCalculator);
});

test('test calculates booking price correctly', function () {
    // Arrange
    $dailyPrice = $this->faker->randomFloat(2, 100, 1000);

    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 5);

    $expectedOriginalPrice = $dailyPrice * 5;

    $car = new CarProjectionDTO(
        id: $this->faker->uuid(),
        brand: $this->faker->word(),
        model: $this->faker->word(),
        year: (int) $this->faker->year(),
        pricePerDay: $dailyPrice,
        isAvailable: true
    );

    $this->priceCalculator
        ->shouldReceive('calculateUsagePrice')
        ->once()
        ->andReturn($expectedOriginalPrice);

    // Act
    $result = $this->service->calculateBookingPrice($car, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('price_service');

test('test handles single day booking', function () {
    // Arrange
    $dailyPrice = $this->faker->randomFloat(2, 100, 1000);

    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 1);
    
    $expectedOriginalPrice = $dailyPrice;

    $car = new CarProjectionDTO(
        id: $this->faker->uuid(),
        brand: $this->faker->word(),
        model: $this->faker->word(),
        year: (int) $this->faker->year(),
        pricePerDay: $dailyPrice,
        isAvailable: true
    );

    $this->priceCalculator
        ->shouldReceive('calculateUsagePrice')
        ->once()
        ->andReturn($expectedOriginalPrice);

    // Act
    $result = $this->service->calculateBookingPrice($car, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('price_service');

test('test calculate additional price', function () {
    // Arrange
    $dailyPrice = $this->faker->randomFloat(2, 100, 1000);

    $end = Carbon::now();
    $actualEnd = $end->clone()->addDays(2);

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
    $dailyPrice = $this->faker->randomFloat(2, 100, 1000);

    $end = Carbon::now();
    $actualEnd = $end;

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
    $originalPrice = $this->faker->randomFloat(2, 100, 1000);
    $additionalPrice = $this->faker->randomFloat(2, 100, 1000);

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