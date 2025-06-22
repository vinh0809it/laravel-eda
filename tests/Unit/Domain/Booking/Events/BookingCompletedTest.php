<?php

declare(strict_types=1);

use Src\Domain\Booking\Events\BookingCompleted;

test('BookingCompleted event has correct event type and aggregate type', function () {
    // Arrange
    $event = new BookingCompleted(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        actualEndDate: fakeDateFromNow(),
        additionalPrice: fakeMoney(),
        finalPrice: fakeMoney()
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
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        actualEndDate: fakeDateFromNow(),
        additionalPrice: fakeMoney(),
        finalPrice: fakeMoney()
    );

    // Act
    $serialized = $event->toArray();
    $deserialized = BookingCompleted::fromArray($serialized);

    // Assert
    expect($deserialized->bookingId)->toBe($event->bookingId);
    expect($deserialized->carId)->toBe($event->carId);
    expect($deserialized->actualEndDate->toDateString())->toBe($event->actualEndDate->toDateString());
    expect($deserialized->additionalPrice)->toBe($event->additionalPrice);
    expect($deserialized->finalPrice)->toBe($event->finalPrice);
})
->group('complete_booking_event');