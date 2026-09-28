<?php

namespace Domain\ScanAnalysis;

/**
 * Value object representing a parsed moon scan
 */
class MoonScan
{
    /**
     * @param array<int, array<Material>> $moonData Moon ID => array of materials
     * @param \DateTime $scanTime
     */
    public function __construct(
        private array $moonData,
        private \DateTime $scanTime
    ) {}

    /**
     * Get all moon IDs in this scan
     *
     * @return array<int>
     */
    public function getMoonIds(): array
    {
        return array_keys($this->moonData);
    }

    /**
     * Get materials for a specific moon
     *
     * @param int $moonId
     * @return array<Material>|null
     */
    public function getMaterialsForMoon(int $moonId): ?array
    {
        return $this->moonData[$moonId] ?? null;
    }

    /**
     * Get all materials across all moons
     *
     * @return array<Material>
     */
    public function getAllMaterials(): array
    {
        return array_merge(...array_values($this->moonData));
    }

    /**
     * Get scan timestamp
     */
    public function getScanTime(): \DateTime
    {
        return $this->scanTime;
    }

    /**
     * Get number of moons in scan
     */
    public function getMoonCount(): int
    {
        return count($this->moonData);
    }
}
