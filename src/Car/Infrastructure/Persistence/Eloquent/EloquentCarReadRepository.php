<?php

declare(strict_types=1);

namespace Src\Car\Infrastructure\Persistence\Eloquent;

use Src\Car\Application\Ports\CarReadRepository;
use Src\Car\Contracts\CarSnapshot;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentRepository;

final class EloquentCarReadRepository extends EloquentRepository implements CarReadRepository
{
    public function __construct(CarModel $model)
    {
        parent::__construct($model);
    }

    public function findCarById(string $carId): ?CarSnapshot
    {
        $row = $this->query()->find($carId);

        return $row ? CarSnapshot::fromArray($row->toArray()) : null;
    }
}
