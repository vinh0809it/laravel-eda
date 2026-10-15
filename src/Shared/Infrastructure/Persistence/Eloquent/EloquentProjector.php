<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Persistence\Eloquent;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A projector keeps one Eloquent read model in sync with the events it
 * subscribes to. Subclasses declare their subscriptions in `subscribe()`,
 * so reading the class tells you exactly which events shape the read model.
 * Each handler is idempotent, keyed in the event processing log by `context()`.
 */
abstract class EloquentProjector
{
    public function __construct(
        protected readonly Model $model
    ) {}

    /**
     * Laravel event subscriber contract: event class => handler method.
     *
     * @return array<class-string, string>
     */
    abstract public function subscribe(Dispatcher $events): array;

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    protected function context(string $method): string
    {
        return static::class . '::' . $method;
    }
}
