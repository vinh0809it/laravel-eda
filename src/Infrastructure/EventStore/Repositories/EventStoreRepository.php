<?php

namespace Src\Infrastructure\EventStore\Repositories;

use Illuminate\Support\Str;
use Src\Infrastructure\EventStore\Models\EventStore;
use Src\Domain\Shared\Repositories\IEventStoreRepository;

class EventStoreRepository implements IEventStoreRepository
{
    public function __construct(
        private readonly EventStore $eventStore
    ) {}

    public function append(string $eventType, string $aggregateType, string $aggregateId, array $eventData, array $metadata = [], int $version = 1): void
    {
        $this->eventStore->create([
            'event_id' => Str::uuid(),
            'event_type' => $eventType,
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_data' => $eventData,
            'metadata' => $metadata,
            'created_at' => now(),
            'version' => $version,
        ]);
    }

    public function getEvents(string $aggregateType, string $aggregateId): array
    {
        return $this->eventStore
            ->where('aggregate_type', $aggregateType)
            ->where('aggregate_id', $aggregateId)
            ->orderBy('version')
            ->get()
            ->toArray();
    }

    public function getLatestVersion(string $aggregateType, string $aggregateId): int
    {
        return $this->eventStore
            ->where('aggregate_type', $aggregateType)
            ->where('aggregate_id', $aggregateId)
            ->max('version') ?? 0;
    }
}
