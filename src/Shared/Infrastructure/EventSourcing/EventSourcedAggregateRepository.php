<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\EventSourcing;

use Illuminate\Contracts\Events\Dispatcher;
use Src\Shared\Application\Ports\EventMapper;
use Src\Shared\Application\Ports\AggregateRepository;
use Src\Shared\Application\Ports\EventStore;
use Src\Shared\Domain\Aggregate\AggregateRoot;

/**
 * Persists an aggregate's recorded events and publishes them in-process.
 *
 * Known limits, tracked for the EDA step: append and publish are not atomic
 * (no transaction, no outbox) and there is no optimistic concurrency check.
 */
final class EventSourcedAggregateRepository implements AggregateRepository
{
    public function __construct(
        private readonly EventStore $eventStore,
        private readonly EventMapper $eventMapper,
        private readonly Dispatcher $events
    ) {}

    public function save(AggregateRoot $aggregate): void
    {
        foreach ($aggregate->getRecordedEvents() as $event) {
            $this->eventStore->append(
                eventType: (new \ReflectionClass($event))->getShortName(),
                aggregateType: $aggregate->getAggregateType(),
                aggregateId: $aggregate->getId(),
                eventData: method_exists($event, 'toArray') ? $event->toArray() : [],
                metadata: [],
                version: $aggregate->getVersion()
            );

            $this->events->dispatch($event);
        }

        $aggregate->clearRecordedEvents();
    }

    public function getEvents(string $aggregateType, string $aggregateId): ?array
    {
        $events = $this->eventStore->getEvents(aggregateType: $aggregateType, aggregateId: $aggregateId);

        return array_map(function (array $event) {
            $eventClass = $this->eventMapper->resolve($event['event_type']);

            return $eventClass::fromArray($event['event_data']);
        }, $events);
    }
}
