<?php

declare(strict_types=1);

use Carbon\Carbon;
use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Enums\BookingStatus;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Booking\Events\BookingCreated;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    
    $this->bookingId = $this->faker->uuid();
    $this->userId = $this->faker->uuid();
    $this->carId = $this->faker->uuid();

    $bookingDates = generateBookingDates($this->faker, 5);
    $this->startDate = $bookingDates['start'];
    $this->endDate = $bookingDates['end'];
    $this->originalPrice = randomMoney($this->faker);
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

    $actualEndDate = Carbon::parse($this->endDate)->addDays(1)->toDateString();
    $additionalPrice = randomMoney($this->faker);
    $finalPrice = randomMoney($this->faker);

    $aggregate->complete(
        actualEndDate: $actualEndDate,
        additionalPrice: $additionalPrice,
        finalPrice: $finalPrice
    );

    expect($aggregate->getStatus())->toBe(BookingStatus::COMPLETED->value)
        ->and($aggregate->getActualEndDate())->toBe($actualEndDate)
        ->and($aggregate->getFinalPrice())->toBe($finalPrice);

    $events = $aggregate->getRecordedEvents();
    expect($events)->toHaveCount(2)
                   ->and($events[1])->toBeInstanceOf(BookingCompleted::class);
})
->group('booking_aggregate');

test('aggregate rebuilds aggregate state from events', function () {

    $actualEndDate = Carbon::parse($this->endDate)->addDays(1)->toDateString();
    $additionalPrice = randomMoney($this->faker);
    $finalPrice = randomMoney($this->faker);

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
        ->and($aggregate->getActualEndDate())->toBe($actualEndDate);
})
->group('booking_aggregate');