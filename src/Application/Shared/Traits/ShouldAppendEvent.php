<?php

namespace Src\Application\Shared\Traits;

use Src\Domain\Shared\Aggregate\AggregateRoot;
use Src\Domain\Shared\Repositories\IEventStoreRepository;

trait ShouldAppendEvent
{
    protected IEventStoreRepository $eventStore;

    public function setEventStore(IEventStoreRepository $eventStore): void
    {
        $this->eventStore = $eventStore;
    }

    protected function persistAggregate(AggregateRoot $aggregate): void
    {
        foreach ($aggregate->getRecordedEvents() as $event) {
            $this->eventStore->append(
                eventType: class_basename($event),
                aggregateType: $aggregate->aggregateType,
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
}