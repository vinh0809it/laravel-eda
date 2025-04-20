<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Pricing\Services;

use Src\Domain\Pricing\Services\PriceService;
use Src\Domain\Pricing\Contracts\IPriceCalculator;
use Src\Application\Car\DTOs\CarProjectionDTO;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->priceCalculator = mock(IPriceCalculator::class);
    $this->service = new PriceService($this->priceCalculator);
});

test('calculates booking price correctly', function () {
    // Arrange
    $dailyPrice = $this->faker->randomFloat(2, 100, 1000);

    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 5);

    $expectedTotalPrice = $dailyPrice * 5;

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
        ->andReturn($expectedTotalPrice);

    // Act
    $result = $this->service->calculateBookingPrice($car, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedTotalPrice);
})
->group('price_service');

test('handles single day booking', function () {
    // Arrange
    $dailyPrice = $this->faker->randomFloat(2, 100, 1000);

    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 1);
    
    $expectedTotalPrice = $dailyPrice;

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
        ->andReturn($expectedTotalPrice);

    // Act
    $result = $this->service->calculateBookingPrice($car, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedTotalPrice);
})
->group('price_service');
