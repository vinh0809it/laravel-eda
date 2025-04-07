<?php

namespace Src\Domain\Pricing\ValueObjects;

class Price
{
    public function __construct(
        private readonly float $amount
    ) {}

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function multiply(int $days): float
    {
        return $this->amount * $days;
    }
} 