<?php

declare(strict_types=1);

use Src\Domain\Booking\Events\BookingCreated;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
});

test('BookingCreated event has correct event type and aggregate type', function () {
    // Arrange
    $event = new BookingCreated(
        bookingId: $this->faker->uuid(),
        carId: $this->faker->uuid(),
        userId: $this->faker->uuid(),
        startDate: $this->faker->date(),
        endDate: $this->faker->date(),
        totalPrice: $this->faker->randomFloat(2, 100, 1000)
    );

    // Assert
    expect($event->getEventType())->toBe('BookingCreated');
    expect($event->getAggregateType())->toBe('Booking');
    expect($event->getAggregateId())->not->toBeEmpty();
})
->group('create_booking_event');

test('BookingCreated event contains all required data', function () {
    // Arrange
    $bookingId = $this->faker->uuid();
    $carId = $this->faker->uuid();
    $userId = $this->faker->uuid();
    $startDate = $this->faker->date();
    $endDate = $this->faker->date();
    $totalPrice = $this->faker->randomFloat(2, 100, 1000);

    // Act
    $event = new BookingCreated(
        bookingId: $bookingId,
        carId: $carId,
        userId: $userId,
        startDate: $startDate,
        endDate: $endDate,
        totalPrice: $totalPrice
    );

    // Assert
    expect($event->bookingId)->toBe($bookingId);
    expect($event->carId)->toBe($carId);
    expect($event->userId)->toBe($userId);
    expect($event->startDate)->toBe($startDate);
    expect($event->endDate)->toBe($endDate);
    expect($event->totalPrice)->toBe($totalPrice);
})
->group('create_booking_event');

test('BookingCreated event can be serialized and deserialized', function () {
    // Arrange
    $event = new BookingCreated(
        bookingId: $this->faker->uuid(),
        carId: $this->faker->uuid(),
        userId: $this->faker->uuid(),
        startDate: $this->faker->date(),
        endDate: $this->faker->date(),
        totalPrice: $this->faker->randomFloat(2, 100, 1000)
    );

    // Act
    $serialized = $event->toArray();
    $deserialized = BookingCreated::fromArray($serialized);

    // Assert
    expect($deserialized->bookingId)->toBe($event->bookingId);
    expect($deserialized->carId)->toBe($event->carId);
    expect($deserialized->userId)->toBe($event->userId);
    expect($deserialized->startDate)->toBe($event->startDate);
    expect($deserialized->endDate)->toBe($event->endDate);
    expect($deserialized->totalPrice)->toBe($event->totalPrice);
})
->group('create_booking_event');