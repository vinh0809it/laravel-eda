<?php

declare(strict_types=1);

namespace Tests\Shared\Unit\EventSourcing;

use Illuminate\Contracts\Events\Dispatcher;
use Src\Shared\Application\Ports\EventMapper;
use Src\Shared\Application\Ports\EventStore;
use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Infrastructure\EventSourcing\EventSourcedAggregateRepository;

beforeEach(function () {
    $this->eventStore = mock(EventStore::class);
    $this->eventMapper = mock(EventMapper::class);
    $this->events = mock(Dispatcher::class);
    $this->service = new EventSourcedAggregateRepository($this->eventStore, $this->eventMapper, $this->events);
    $this->aggregate = mock(AggregateRoot::class);
});

test('appends every recorded event to the store and publishes it', function () {
    $event = new class {
        public function toArray(): array { return ['event' => 'data']; }
    };
    $shortName = (new \ReflectionClass($event))->getShortName();

    $this->aggregate->shouldReceive('getRecordedEvents')->andReturn([$event]);
    $this->aggregate->shouldReceive('getAggregateType')->andReturn('Booking');
    $this->aggregate->shouldReceive('getId')->andReturn('booking-id');
    $this->aggregate->shouldReceive('getVersion')->andReturn(1);
    $this->aggregate->shouldReceive('clearRecordedEvents')->once();

    $this->eventStore->shouldReceive('append')->once()
        ->with($shortName, 'Booking', 'booking-id', ['event' => 'data'], [], 1);
    $this->events->shouldReceive('dispatch')->once()->with($event);

    $this->service->save($this->aggregate);
})
->group('event_sourcing_service');

test('loads events from the store and deserializes them through the mapper', function () {
    $eventInstance = mock();
    $eventInstance->shouldReceive('fromArray')->andReturnSelf();

    $this->eventStore->shouldReceive('getEvents')->with('Booking', 'booking-id')
        ->andReturn([['event_type' => 'BookingCreated', 'event_data' => ['event' => 'data']]]);
    $this->eventMapper->shouldReceive('resolve')->with('BookingCreated')->andReturn(get_class($eventInstance));

    expect($this->service->getEvents('Booking', 'booking-id'))->toHaveCount(1);
})
->group('event_sourcing_service');
