<?php

declare(strict_types=1);

use Src\Domain\Booking\Events\BookingCreated;

test('BookingCreated event has correct event type and aggregate type', function () {
    // Arrange
    $event = new BookingCreated(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        userId: fakeUuid(),
        startDate: fakeDateFromNow(),
        endDate: fakeDateFromNow(),
        originalPrice: faker()->randomFloat(2, 100, 1000)
    );

    // Assert
    expect($event->getEventType())->toBe('BookingCreated');
    expect($event->getAggregateType())->toBe('Booking');
    expect($event->getAggregateId())->not->toBeEmpty();
})
->group('create_booking_event');

test('BookingCreated event can be serialized and deserialized', function () {
    // Arrange
    $event = new BookingCreated(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        userId: fakeUuid(),
        startDate: fakeDateFromNow(),
        endDate: fakeDateFromNow(),
        originalPrice: faker()->randomFloat(2, 100, 1000)
    );

    // Act
    $serialized = $event->toArray();
    $deserialized = BookingCreated::fromArray($serialized);

    // Assert
    expect($deserialized->bookingId)->toBe($event->bookingId);
    expect($deserialized->carId)->toBe($event->carId);
    expect($deserialized->userId)->toBe($event->userId);
    expect($deserialized->startDate->toDateString())->toBe($event->startDate->toDateString());
    expect($deserialized->endDate->toDateString())->toBe($event->endDate->toDateString());
    expect($deserialized->originalPrice)->toBe($event->originalPrice);
})
->group('create_booking_event');