<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Services;

use Src\Domain\Shared\Aggregate\AggregateRoot;

class EventSourcingService extends EventSourcingHandler implements IEventSourcingService
{
    public function save(AggregateRoot $aggregate): void
    {
        $this->persistAggregate($aggregate);
    }

    public function getEvents(string $aggregateType, string $aggregateId): ?array
    {
        return $this->loadEventStore($aggregateType, $aggregateId);
    }
}