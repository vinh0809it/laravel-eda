<?php

namespace Src\Infrastructure\Shared\Projections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Src\Domain\Shared\Projections\IBaseProjection;

abstract class BaseProjection implements IBaseProjection
{
    public function __construct(
        protected readonly Model $model
    ) {}

    protected function context(string $method): string
    {
        return static::class . '::' . $method;
    }

    public function findById(string $id): Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }
} 