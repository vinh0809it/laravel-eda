<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Persistence\Eloquent;

use Src\Shared\Application\Ports\EventProcessLogger;
use Src\Shared\Infrastructure\Persistence\Eloquent\EventProcessingLogModel;

class EloquentEventProcessLogger implements EventProcessLogger
{
    public function hasProcessed(string $eventId, string $listener): bool
    {
        return EventProcessingLogModel::where('event_id', $eventId)
            ->where('listener', $listener)
            ->where('status', 'success')
            ->exists();
    }

    public function markSuccess(string $eventId, string $listener): void
    {
        EventProcessingLogModel::updateOrCreate(
            [
                'event_id' => $eventId,
                'listener' => $listener,
            ],
            [
                'status' => 'success',
                'error' => null,
                'processed_at' => now(),
            ]
        );
    }

    public function markFailure(string $eventId, string $listener, string $error): void
    {
        EventProcessingLogModel::updateOrCreate(
            [
                'event_id' => $eventId,
                'listener' => $listener,
            ],
            [
                'status' => 'failed',
                'error' => $error,
                'processed_at' => now(),
            ]
        );
    }

    public function getLastError(string $eventId, string $listener): ?string
    {
        $log = EventProcessingLogModel::where('event_id', $eventId)
            ->where('listener', $listener)
            ->where('status', 'failed')
            ->latest()
            ->first();

        return $log?->error;
    }
} 