<?php

namespace Src\Domain\Shared\Aggregate;

abstract class AggregateRoot
{
    protected int $version = 0;
    protected array $recordedEvents = [];

    public string $aggregateType;

    abstract public function getId(): string;

    public function getVersion(): int
    {
        return $this->version;
    }

    public function incrementVersion(): void
    {
        $this->version++;
    }
    
    protected function recordEvent($event): void 
    {
        $this->recordedEvents[] = $event;
    }

    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function clearRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }
}