<?php

declare(strict_types=1);

namespace Src\Infrastructure\Shared\Loggers;

use Src\Domain\Shared\Loggers\IEventProcessLogger;
use Src\Infrastructure\Shared\Loggers\Models\EventProcessingLog;

class EloquentEventProcessLogger implements IEventProcessLogger
{
    public function hasProcessed(string $eventId, string $listener): bool
    {
        return EventProcessingLog::where('event_id', $eventId)
            ->where('listener', $listener)
            ->where('status', 'success')
            ->exists();
    }

    public function markSuccess(string $eventId, string $listener): void
    {
        EventProcessingLog::updateOrCreate(
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
        EventProcessingLog::updateOrCreate(
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
        $log = EventProcessingLog::where('event_id', $eventId)
            ->where('listener', $listener)
            ->where('status', 'failed')
            ->latest()
            ->first();

        return $log?->error;
    }
} 