<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Pricing\Calculators;

use Src\Domain\Pricing\Calculators\SimplePriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    $this->calculator = new SimplePriceCalculator();
});

test('calculates price for standard booking duration', function () {
    // Arrange
    $dailyPrice = new Price(100.00);
    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 5);
    $expectedTotalPrice = 500.00;

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedTotalPrice);
})
->group('simple_price_calculator');

test('calculates price for single day booking', function () {
    // Arrange
    $dailyPrice = new Price(100.00);
    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 1);
    $expectedTotalPrice = 100.00;

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedTotalPrice);
})
->group('simple_price_calculator');

test('calculates price with different daily rates', function () {
    // Arrange
    $dailyPrice = new Price(250.00);
    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 5);
    $expectedTotalPrice = 1250.00;

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedTotalPrice);
})
->group('simple_price_calculator');

test('handles fractional daily rates', function () {
    // Arrange
    $dailyPrice = new Price(99.99);
    ['start' => $startDate, 'end' => $endDate] = generateBookingDates($this->faker, 5);
    $expectedTotalPrice = 499.95;

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedTotalPrice);
})
->group('simple_price_calculator');
