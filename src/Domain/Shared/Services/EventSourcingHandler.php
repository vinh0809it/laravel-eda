<?php

namespace Src\Domain\Shared\Services;

use Src\Domain\Shared\Aggregate\AggregateRoot;
use Src\Domain\Shared\EventStore\IEventMapper;
use Src\Domain\Shared\EventStore\IEventStoreRepository;

abstract class EventSourcingHandler
{
    public function __construct(
        protected IEventStoreRepository $eventStore,
        protected IEventMapper $eventMapper
    ) {}

    protected function persistAggregate(AggregateRoot $aggregate): void
    {
        foreach ($aggregate->getRecordedEvents() as $event) {
            $this->eventStore->append(
                eventType: class_basename($event),
                aggregateType: $aggregate->getAggregateType(),
                aggregateId: $aggregate->getId(),
                eventData: method_exists($event, 'toArray') ? $event->toArray() : [],
                metadata: [],
                version: $aggregate->getVersion()
            );
            
            // event dispatch
            event($event);
        }

        $aggregate->clearRecordedEvents();
    }

    protected function loadEventStore(string $aggregateType, string $aggregateId): ?array
    {
        $events = $this->eventStore->getEvents(
            aggregateType: $aggregateType,
            aggregateId: $aggregateId
        );

        $events = array_map(function ($event) {
            $eventType = $this->eventMapper->resolve($event['event_type']);
            return $eventType::fromArray($event['event_data']);
        }, $events);

        return $events;
    }
}