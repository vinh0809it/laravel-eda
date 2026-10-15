<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository
{
    public function __construct(
        protected readonly Model $model
    ) {}

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }
}
