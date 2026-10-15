<?php

declare(strict_types=1);

namespace Tests\Car\Unit\Application\Listeners;

use Illuminate\Contracts\Events\Dispatcher;
use Src\Booking\Domain\Events\BookingCompleted;
use Src\Car\Application\Listeners\RecalcFeeOnBookingCompletedListener;
use Src\Car\Application\Ports\CarReadRepository;
use Src\Car\Application\Ports\PopularityFeeCalculator;
use Src\Car\Contracts\CarSnapshot;
use Src\Car\Domain\Events\PopularityFeeRecalculated;

beforeEach(function () {
    $this->cars = mock(CarReadRepository::class);
    $this->feeCalculator = mock(PopularityFeeCalculator::class);
    $this->events = mock(Dispatcher::class);
    $this->listener = new RecalcFeeOnBookingCompletedListener($this->cars, $this->feeCalculator, $this->events);

    $this->carId = fakeUuid();
    $this->event = new BookingCompleted(
        bookingId: fakeUuid(),
        carId: $this->carId,
        actualEndDate: fakeDateFromNow(),
        additionalPrice: fakeMoney(),
        finalPrice: fakeMoney(),
        completionNote: faker()->sentence()
    );
});

test('dispatches PopularityFeeRecalculated with the recalculated fee', function () {
    $car = new CarSnapshot(
        id: $this->carId,
        brand: faker()->word(),
        model: faker()->word(),
        year: (int) faker()->year(),
        pricePerDay: fakeMoney(),
        popularityFee: fakeMoney(),
        bookedCount: faker()->randomNumber()
    );
    $expectedFee = fakeMoney();

    $this->cars->shouldReceive('findCarById')->with($this->carId)->andReturn($car);
    $this->feeCalculator->shouldReceive('calculatePopularityFee')->with($car->pricePerDay, $car->bookedCount)->andReturn($expectedFee);
    $this->events->shouldReceive('dispatch')->once()->withArgs(
        fn (PopularityFeeRecalculated $e) => $e->carId === $this->carId && $e->newPopularityFee === $expectedFee
    );

    $this->listener->handle($this->event);
})
->group('car_event_listener');

test('does nothing when the car is unknown', function () {
    $this->cars->shouldReceive('findCarById')->with($this->carId)->andReturnNull();
    $this->events->shouldNotReceive('dispatch');

    $this->listener->handle($this->event);
})
->group('car_event_listener');
