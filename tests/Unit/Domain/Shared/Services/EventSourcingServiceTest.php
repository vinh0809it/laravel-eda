<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Services;

use Illuminate\Support\Facades\Event;
use Src\Domain\Shared\Aggregate\AggregateRoot;
use Src\Domain\Shared\EventStore\IEventMapper;
use Src\Domain\Shared\EventStore\IEventStoreRepository;
use Src\Domain\Shared\Services\EventSourcingService;

beforeEach(function () {
    $this->eventStore = mock(IEventStoreRepository::class);
    $this->eventMapper = mock(IEventMapper::class);
    $this->service = new EventSourcingService($this->eventStore, $this->eventMapper);

    $this->aggregate = mock(AggregateRoot::class);
});

class FakeEvent {
    public function toArray(): array {
        return ['event' => 'data'];
    }
}

test('persists aggregate and dispatch an event', function () {
    // Arrange
    Event::fake();

    $mockEvent = new FakeEvent();
    $this->aggregate->shouldReceive('getRecordedEvents')->andReturn([$mockEvent]);
    $this->aggregate->shouldReceive('getAggregateType')->andReturn('Booking');
    $this->aggregate->shouldReceive('getId')->andReturn('booking-id');
    $this->aggregate->shouldReceive('getVersion')->andReturn(1);
    $this->aggregate->shouldReceive('clearRecordedEvents');

    $this->eventStore->shouldReceive('append')
        ->once()
        ->with(
            'FakeEvent',
            'Booking',
            'booking-id',
            ['event' => 'data'],
            [],
            1
        );
    // Act
    $this->service->save($this->aggregate);

    Event::assertDispatched(FakeEvent::class);
})
->group('event_sourcing_service');

test('loads event from the event store and deserializes events', function () {
    // Arrange

    $aggregateType = 'Booking';
    $aggregateId = 'booking-id';

    $mockEventData = [
        ['event_type' => 'BookingCreated', 'event_data' => ['event' => 'data']]
    ];

    $mockEventInstance = mock();
    $mockEventInstance->shouldReceive('fromArray')->andReturnSelf();

    $this->eventStore->shouldReceive('getEvents')
        ->with($aggregateType, $aggregateId)
        ->andReturn($mockEventData);
    
    $this->eventMapper->shouldReceive('resolve')
        ->with('BookingCreated')
        ->andReturn(get_class($mockEventInstance));
        
    // Act
    $events = $this->service->getEvents($aggregateType, $aggregateId);

    expect($events)->toHaveCount(1);

})
->group('event_sourcing_service');