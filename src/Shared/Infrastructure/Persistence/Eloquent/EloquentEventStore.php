<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Persistence\Eloquent;

use Illuminate\Support\Str;
use Src\Shared\Application\Ports\EventStore;

final class EloquentEventStore implements EventStore
{
    public function __construct(
        private readonly EventStoreModel $events
    ) {}

    public function append(string $eventType, string $aggregateType, string $aggregateId, array $eventData, array $metadata = [], int $version = 1): void
    {
        $this->events->newQuery()->create([
            'event_id' => Str::uuid()->toString(),
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
        return $this->events->newQuery()
            ->where('aggregate_type', $aggregateType)
            ->where('aggregate_id', $aggregateId)
            ->orderBy('version')
            ->get()
            ->toArray();
    }

    public function getLatestVersion(string $aggregateType, string $aggregateId): int
    {
        return (int) $this->events->newQuery()
            ->where('aggregate_type', $aggregateType)
            ->where('aggregate_id', $aggregateId)
            ->max('version');
    }
}
