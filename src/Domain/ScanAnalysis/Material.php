<?php

namespace Domain\ScanAnalysis;

/**
 * Value object representing a material in a moon scan
 */
class Material
{
    public function __construct(
        private string $name,
        private float $quantity
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

    /**
     * Calculate ISK value of this material
     *
     * @param float $unitPrice Price per unit in ISK
     * @return float
     */
    public function calculateValue(float $unitPrice): float
    {
        return $this->quantity * $unitPrice;
    }
}
