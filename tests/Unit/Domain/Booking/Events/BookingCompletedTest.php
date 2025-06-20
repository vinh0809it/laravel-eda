<?php

declare(strict_types=1);

use Src\Domain\Booking\Events\BookingCompleted;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
});

test('BookingCompleted event has correct event type and aggregate type', function () {
    // Arrange
    $event = new BookingCompleted(
        bookingId: $this->faker->uuid(),
        carId: $this->faker->uuid(),
        actualEndDate: $this->faker->date(),
        additionalPrice: $this->faker->randomFloat(2, 100, 1000),
        finalPrice: $this->faker->randomFloat(2, 100, 1000)
    );

    // Assert
    expect($event->getEventType())->toBe('BookingCompleted');
    expect($event->getAggregateType())->toBe('Booking');
    expect($event->getAggregateId())->not->toBeEmpty();
})
->group('complete_booking_event');

test('BookingCompleted event can be serialized and deserialized', function () {
    // Arrange
    $event = new BookingCompleted(
        bookingId: $this->faker->uuid(),
        carId: $this->faker->uuid(),
        actualEndDate: $this->faker->date(),
        additionalPrice: $this->faker->randomFloat(2, 100, 1000),
        finalPrice: $this->faker->randomFloat(2, 100, 1000)
    );

    // Act
    $serialized = $event->toArray();
    $deserialized = BookingCompleted::fromArray($serialized);

    // Assert
    expect($deserialized->bookingId)->toBe($event->bookingId);
    expect($deserialized->carId)->toBe($event->carId);
    expect($deserialized->actualEndDate)->toBe($event->actualEndDate);
    expect($deserialized->additionalPrice)->toBe($event->additionalPrice);
    expect($deserialized->finalPrice)->toBe($event->finalPrice);
})
->group('complete_booking_event');