<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Car\Listeners;

use Illuminate\Support\Facades\Log;
use Src\Application\Car\Listeners\PopularityFeeRecalculatedListener;
use Src\Domain\Car\Events\PopularityFeeRecalculated;

beforeEach(function () {
    $this->listener = new PopularityFeeRecalculatedListener();
});

test('listener logs popularity fee recalculation event', function () {
    // Arrange
    $event = new PopularityFeeRecalculated(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        newPopularityFee: fakeMoney()
    );

    // Assert log will be called
    Log::shouldReceive('info')
        ->once()
        ->with('Popularity Fee Recalculated', [
            'booking_id' => $event->bookingId,
            'car_id' => $event->carId,
            'popularity_fee' => $event->newPopularityFee
        ]);

    // Act
    $this->listener->handle($event);
})
->group('car_event_listener'); 