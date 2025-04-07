<?php

declare(strict_types=1);

namespace Src\Application\Shared\DTOs;

use Src\Domain\Shared\Interfaces\IPaginationResult;

class PaginationDTO implements IPaginationResult
{
    public function __construct(
        public readonly array $items,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly int $total,
        public readonly int $lastPage
    ) 
    {}

    public function items(): array
    {
        return $this->items;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }
    
    public function total(): int
    {
        return $this->total;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }   
} 