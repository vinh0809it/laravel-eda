<?php

declare(strict_types=1);

use Src\Domain\Booking\Events\BookingChanged;

test('BookingChanged event has correct event type and aggregate type', function () {
    // Arrange
    $event = new BookingChanged(
        bookingId: fakeUuid(),
        newStartDate: fakeDateFromNow(),
        newEndDate: fakeDateFromNow(2),
        newOriginalPrice: fakeMoney()
    );

    // Assert
    expect($event->getEventType())->toBe('BookingChanged');
    expect($event->getAggregateType())->toBe('Booking');
    expect($event->getAggregateId())->not->toBeEmpty();
})
->group('change_booking_event');

test('BookingCreated event can be serialized and deserialized', function () {
    // Arrange
    $event = new BookingChanged(
        bookingId: fakeUuid(),
        newStartDate: fakeDateFromNow(),
        newEndDate: fakeDateFromNow(2),
        newOriginalPrice: fakeMoney()
    );

    // Act
    $serialized = $event->toArray();
    $deserialized = BookingChanged::fromArray($serialized);

    // AssertnewEndDate
    expect($deserialized->bookingId)->toBe($event->bookingId);
    expect($deserialized->newStartDate->toDateString())->toBe($event->newStartDate->toDateString());
    expect($deserialized->newEndDate->toDateString())->toBe($event->newEndDate->toDateString());
    expect($deserialized->newOriginalPrice)->toBe($event->newOriginalPrice);
})
->group('change_booking_event');