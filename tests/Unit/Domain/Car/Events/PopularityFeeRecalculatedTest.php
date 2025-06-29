<?php

declare(strict_types=1);

use Src\Domain\Car\Events\PopularityFeeRecalculated;

test('PopularityFeeRecalculated event has correct event type and aggregate type', function () {
    // Arrange
    $event = new PopularityFeeRecalculated(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        newPopularityFee: fakeMoney()
    );

    // Assert
    expect($event->getEventType())->toBe('PopularityFeeRecalculated');
    expect($event->getAggregateType())->toBe('Car');
    expect($event->getAggregateId())->not->toBeEmpty();
})
->group('popularity_fee_recalculated_event');

test('PopularityFeeRecalculated event can be serialized and deserialized', function () {
    // Arrange
    $event = new PopularityFeeRecalculated(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        newPopularityFee: fakeMoney()
    );

    // Act
    $serialized = $event->toArray();
    $deserialized = PopularityFeeRecalculated::fromArray($serialized);

    // AssertnewEndDate
    expect($deserialized->bookingId)->toBe($event->bookingId);
    expect($deserialized->carId)->toBe($event->carId);
    expect($deserialized->newPopularityFee)->toBe($event->newPopularityFee);
})
->group('popularity_fee_recalculated_event');