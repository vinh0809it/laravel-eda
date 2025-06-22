<?php

declare(strict_types=1);

use Carbon\Carbon;
use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Booking\Events\BookingCreated;
use Src\Domain\Booking\Events\BookingChanged;
use Src\Domain\Shared\Enums\HttpStatusCode;
use Src\Domain\Shared\Exceptions\BussinessException;

beforeEach(function () {
    $this->bookingId = fakeUuid();
    $this->userId = fakeUuid();
    $this->carId = fakeUuid();

    $this->startDate = fakeDateFromNow();
    $this->endDate = fakeDateFromNow(5);
    $this->originalPrice = fakeMoney();
});


test('aggregate creates a booking and record a BookingCreated event', function () {
    $aggregate = BookingAggregate::create(
        id: $this->bookingId,
        carId: $this->carId,            
        userId: $this->userId,
        startDate: $this->startDate,
        endDate: $this->endDate,
        originalPrice: $this->originalPrice
    );

    expect($aggregate->getId())->toBe($this->bookingId);
    expect($aggregate->getStatus())->toBe(BookingStatus::CREATED->value);

    $events = $aggregate->getRecordedEvents();
    expect($events)->toHaveCount(1)
                   ->and($events[0])->toBeInstanceOf(BookingCreated::class);
})
->group('booking_aggregate');

test('aggregate completes a booking and records a BookingCompleted event', function () {
    $aggregate = BookingAggregate::create(
        id: $this->bookingId,
        carId: $this->carId,            
        userId: $this->userId,
        startDate: $this->startDate,
        endDate: $this->endDate,
        originalPrice: $this->originalPrice
    );

    $actualEndDate = Carbon::parse($this->endDate)->addDays(1);
    $additionalPrice = fakeMoney();
    $finalPrice = fakeMoney();

    $aggregate->complete(
        actualEndDate: $actualEndDate,
        additionalPrice: $additionalPrice,
        finalPrice: $finalPrice
    );

    expect($aggregate->getStatus())->toBe(BookingStatus::COMPLETED->value)
        ->and($aggregate->getActualEndDate()->toDateString())->toBe($actualEndDate->toDateString())
        ->and($aggregate->getFinalPrice())->toBe($finalPrice);

    $events = $aggregate->getRecordedEvents();
    expect($events)->toHaveCount(2)
                   ->and($events[1])->toBeInstanceOf(BookingCompleted::class);
})
->group('booking_aggregate');

test('test aggregate can not complete the booking which is completed', function () {
    $aggregate = BookingAggregate::create(
        id: $this->bookingId,
        carId: $this->carId,            
        userId: $this->userId,
        startDate: $this->startDate,
        endDate: $this->endDate,
        originalPrice: $this->originalPrice
    );

    $actualEndDate = Carbon::parse($this->endDate)->addDays(1);
    $additionalPrice = fakeMoney();
    $finalPrice = fakeMoney();

    $aggregate->complete(
        actualEndDate: $actualEndDate,
        additionalPrice: $additionalPrice,
        finalPrice: $finalPrice
    );

    expect(fn () => 
        $aggregate->complete(
            actualEndDate: $actualEndDate,
            additionalPrice: $additionalPrice,
            finalPrice: $finalPrice
        )
    )->toThrow(function (BussinessException $e) {
        expect($e->getCode())->toBe(HttpStatusCode::CONFLICT->value);
        expect($e->getMessage())->toBe('Booking is already completed!');
    });
})
->group('booking_aggregate');

test('test aggregate changes a booking and records a BookingChanged event', function () {

    $aggregate = BookingAggregate::create(
        id: $this->bookingId,
        carId: $this->carId,            
        userId: $this->userId,
        startDate: $this->startDate,
        endDate: $this->endDate,
        originalPrice: $this->originalPrice
    );

    $changedStartDate = Carbon::now();
    $changedEndDate = $changedStartDate->clone()->addDays(2);
    $newOriginalPrice = fakeMoney();

    $aggregate->change(
        newStartDate: $changedStartDate,
        newEndDate: $changedEndDate,
        newOriginalPrice: $newOriginalPrice
    );

    expect($aggregate->getStatus())->toBe(BookingStatus::CHANGED->value)
        ->and($aggregate->getStartDate()->toDateString())->toBe($changedStartDate->toDateString())
        ->and($aggregate->getEndDate()->toDateString())->toBe($changedEndDate->toDateString())
        ->and($aggregate->getOriginalPrice())->toBe($newOriginalPrice);

    $events = $aggregate->getRecordedEvents();
    expect($events)->toHaveCount(2)
                   ->and($events[1])->toBeInstanceOf(BookingChanged::class);
})
->group('booking_aggregate');


test('test aggregate rebuilds aggregate state from events', function () {

    $actualEndDate = Carbon::parse($this->endDate)->addDays(1);
    $additionalPrice = fakeMoney();
    $finalPrice = fakeMoney();

    $events = [
        new BookingCreated(
            bookingId: $this->bookingId,
            carId: $this->carId,
            userId: $this->userId,
            startDate: $this->startDate,
            endDate: $this->endDate,
            originalPrice: $this->originalPrice
        ),
        new BookingCompleted(
            bookingId: $this->bookingId,
            carId: $this->carId,
            actualEndDate: $actualEndDate,
            additionalPrice: $additionalPrice,
            finalPrice: $finalPrice
        )
    ];

    $aggregate = BookingAggregate::replayEvents($events);

    expect($aggregate->getStatus())->toBe(BookingStatus::COMPLETED->value)
        ->and($aggregate->getFinalPrice())->toBe($finalPrice)
        ->and($aggregate->getActualEndDate()->toDateString())->toBe($actualEndDate->toDateString());
})
->group('booking_aggregate');