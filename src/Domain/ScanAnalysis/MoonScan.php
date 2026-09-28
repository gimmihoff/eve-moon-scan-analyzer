<?php

namespace Domain\ScanAnalysis;

class MoonScan
{
    /**
     * @param array<int, array<Material>> $moonData
     */
    public function __construct(
        private readonly array $moonData,
        private readonly \DateTime $scanTime
    ) {
    }

    /**
     * @return array<int>
     */
    public function getMoonIds(): array
    {
        return array_keys($this->moonData);
    }

    /**
     * @param int $moonId
     * @return array<Material>|null
     */
    public function getMaterialsForMoon(int $moonId): ?array
    {
        return $this->moonData[$moonId] ?? null;
    }

    public function getMoonCount(): int
    {
        return count($this->moonData);
    }
}
