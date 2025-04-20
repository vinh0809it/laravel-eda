<?php

declare(strict_types=1);

use Src\Domain\Booking\Aggregates\BookingAggregate;
use Src\Domain\Booking\Events\BookingCreated;

beforeEach(function () {
    $this->faker = \Faker\Factory::create();
    
    // Create test data with primitive types
    $this->bookingId = $this->faker->uuid();
    $this->userId = $this->faker->uuid();
    $this->carId = $this->faker->uuid();
    $this->startDate = $this->faker->date();
    $this->endDate = $this->faker->date();
    $this->totalPrice = $this->faker->randomFloat(2, 100, 1000);
});

test('aggregate creates booking with correct initial state', function () {
    // Act
    $aggregate = BookingAggregate::create(
        id: $this->bookingId,
        carId: $this->carId,            
        userId: $this->userId,
        startDate: $this->startDate,
        endDate: $this->endDate,
        totalPrice: $this->totalPrice
    );

    // Assert
    expect($aggregate->toArray())->toMatchArray([
        'id' => $this->bookingId,
        'car_id' => $this->carId,
        'user_id' => $this->userId,
        'start_date' => $this->startDate,
        'end_date' => $this->endDate,
        'total_price' => $this->totalPrice,
    ]);

    expect($aggregate->getStatus())->toBe('created');
    expect($aggregate->getVersion())->toBe(1);

    $events = $aggregate->getRecordedEvents();
    expect($events)->toHaveCount(1);

    tap($events[0], function ($event) {
        expect($event)->toBeInstanceOf(BookingCreated::class);
        expect($event->bookingId)->toBe($this->bookingId);
        expect($event->userId)->toBe($this->userId);
        expect($event->carId)->toBe($this->carId);
        expect($event->startDate)->toBe($this->startDate);
        expect($event->endDate)->toBe($this->endDate);
        expect($event->totalPrice)->toBe($this->totalPrice);
    });
})
->group('booking_aggregate');
