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
        return self::class . '::' . $method;
    }

    public function findById(string $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(string $id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }
} 