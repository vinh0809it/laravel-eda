<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Pricing\Calculators;

use Src\Domain\Pricing\Calculators\SimplePriceCalculator;
use Src\Domain\Pricing\ValueObjects\Price;

beforeEach(function () {
    $this->calculator = new SimplePriceCalculator();
});

test('calculates price for standard booking duration', function () {
    // Arrange
    $dailyPrice = new Price(100.00);
    $expectedOriginalPrice = 500.00;

    $startDate = fakeDateFromNow();
    $endDate = fakeDateFromNow(5);

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('simple_price_calculator');

test('calculates price for single day booking', function () {
    // Arrange
    $dailyPrice = new Price(100.00);
    $expectedOriginalPrice = 100.00;

    $startDate = fakeDateFromNow();
    $endDate = fakeDateFromNow(1);

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('simple_price_calculator');

test('calculates price with different daily rates', function () {
    // Arrange
    $dailyPrice = new Price(250.00);
    $expectedOriginalPrice = 1250.00;

    $startDate = fakeDateFromNow();
    $endDate = fakeDateFromNow(5);

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('simple_price_calculator');

test('handles fractional daily rates', function () {
    // Arrange
    $dailyPrice = new Price(99.99);
    $expectedOriginalPrice = 499.95;

    $startDate = fakeDateFromNow();
    $endDate = fakeDateFromNow(5);

    // Act
    $result = $this->calculator->calculateUsagePrice($dailyPrice, $startDate, $endDate);

    // Assert
    expect($result)->toBe($expectedOriginalPrice);
})
->group('simple_price_calculator');
