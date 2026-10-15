<?php

declare(strict_types=1);

namespace Tests\Car\Unit\Application\Listeners;

use Psr\Log\LoggerInterface;
use Src\Car\Application\Listeners\PopularityFeeRecalculatedListener;
use Src\Car\Domain\Events\PopularityFeeRecalculated;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->listener = new PopularityFeeRecalculatedListener($this->logger);
});

test('listener logs popularity fee recalculation event', function () {
    // Arrange
    $event = new PopularityFeeRecalculated(
        bookingId: fakeUuid(),
        carId: fakeUuid(),
        newPopularityFee: fakeMoney()
    );

    // Assert log will be called
    $this->logger->shouldReceive('info')
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