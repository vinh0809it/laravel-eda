<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Car\Listeners;

use Illuminate\Support\Facades\Event;
use Src\Application\Car\Listeners\RecalcFeeOnBookingCompletedListener;
use Src\Domain\Booking\Events\BookingCompleted;
use Src\Domain\Car\Events\PopularityFeeRecalculated;
use Src\Domain\Car\Services\ICarService;
use Src\Domain\Car\Snapshots\CarSnapshot;
use Src\Domain\Pricing\Services\IPriceService;

beforeEach(function () {
    $this->carService = mock(ICarService::class);
    $this->priceService = mock(IPriceService::class);
    $this->listener = new RecalcFeeOnBookingCompletedListener($this->carService, $this->priceService);
});

test('listener dispatches popularity fee recalculated event on booking completed', function () {
    // Arrange
    $carId = fakeUuid();
    
    $carSnapshot = new CarSnapshot(
        id: $carId,
        brand: faker()->word(),
        model: faker()->word(),
        year: (int) faker()->year(),
        pricePerDay: fakeMoney(),
        popularityFee: fakeMoney(),
        bookedCount: faker()->randomNumber()
    );

    $this->carService->shouldReceive('findCarById')
        ->with($carId)
        ->andReturn($carSnapshot);

    $expectedFee = fakeMoney();

    $this->priceService->shouldReceive('calculatePopularityFee')
        ->with($carSnapshot->pricePerDay, $carSnapshot->bookedCount)
        ->andReturn($expectedFee);

    Event::fake();

    $event = new BookingCompleted(
        bookingId: fakeUuid(),
        carId: $carId,
        actualEndDate: fakeDateFromNow(),
        additionalPrice: fakeMoney(),
        finalPrice: fakeMoney(),
        completionNote: faker()->sentence()
    );

    // Act
    $this->listener->handle($event);

    // Assert
    
    Event::assertDispatched(PopularityFeeRecalculated::class, function ($dispatchedEvent) use ($carId, $expectedFee) {
        return $dispatchedEvent->carId === $carId &&
               $dispatchedEvent->newPopularityFee === $expectedFee;
    });
})
->group('car_event_listener'); 