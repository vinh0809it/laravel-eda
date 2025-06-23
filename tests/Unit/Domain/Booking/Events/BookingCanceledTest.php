<?php

declare(strict_types=1);

use Src\Domain\Booking\Events\BookingCanceled;

test('BookingCanceled event has correct event type and aggregate type', function () {
    // Arrange
    $event = new BookingCanceled(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        canceledAt: now(),
        cancelReason: faker()->sentence()
    );

    // Assert
    expect($event->getEventType())->toBe('BookingCanceled');
    expect($event->getAggregateType())->toBe('Booking');
    expect($event->getAggregateId())->not->toBeEmpty();
})
->group('cancel_booking_event');

test('BookingCanceled event can be serialized and deserialized', function () {
    // Arrange
    $event = new BookingCanceled(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        canceledAt: now(),
        cancelReason: faker()->sentence()
    );

    // Act
    $serialized = $event->toArray();
    $deserialized = BookingCanceled::fromArray($serialized);

    // AssertnewEndDate
    expect($deserialized->bookingId)->toBe($event->bookingId);
    expect($deserialized->canceledAt->toDateTimeString())->toBe($event->canceledAt->toDateTimeString());
    expect($deserialized->cancelReason)->toBe($event->cancelReason);
})
->group('cancel_booking_event');