<?php

declare(strict_types=1);

namespace Src\Domain\Shared\Services;

use Src\Application\Shared\Traits\ShouldAppendEvent;
use Src\Domain\Shared\Aggregate\AggregateRoot;
use Src\Domain\Shared\Repositories\IEventStoreRepository;

class EventSourcingService implements IEventSourcingService
{
    use ShouldAppendEvent;

    public function __construct(
        private IEventStoreRepository $eventStoreRepository
    ) {
        $this->setEventStore($eventStoreRepository);
    }

    public function save(AggregateRoot $aggregate): void
    {
        $this->persistAggregate($aggregate);
    }

    public function getEvents(string $aggregateType, string $aggregateId): ?array
    {
        return $this->loadEventStore($aggregateType, $aggregateId);
    }
}