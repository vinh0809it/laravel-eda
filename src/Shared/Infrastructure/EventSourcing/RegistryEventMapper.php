<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\EventSourcing;

use Src\Shared\Application\Ports\EventMapper;
use Src\Shared\Infrastructure\Exceptions\UnknownEventException;

/**
 * Maps the short event name stored in the event store back to its class.
 * Every module registers the events it owns through its ModuleServiceProvider.
 */
final class RegistryEventMapper implements EventMapper
{
    /** @var array<string, class-string> short name => class */
    private array $map = [];

    /** @param list<class-string> $eventClasses */
    public function register(array $eventClasses): void
    {
        foreach ($eventClasses as $class) {
            $this->map[substr($class, strrpos($class, '\\') + 1)] = $class;
        }
    }

    public function resolve(string $eventShortName): string
    {
        return $this->map[$eventShortName]
            ?? throw new UnknownEventException("Unknown event type: $eventShortName");
    }
}
