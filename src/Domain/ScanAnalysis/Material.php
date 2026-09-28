<?php

namespace Domain\ScanAnalysis;

class Material
{
    public function __construct(
        private readonly string $name,
        private readonly float $quantity
    ) {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }
}
