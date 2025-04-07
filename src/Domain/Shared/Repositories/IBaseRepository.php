<?php

namespace Src\Domain\Shared\Repositories;

interface IBaseRepository
{
    public function findById(string $id);
    public function all();
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
} 