<?php

namespace App\Service;

use Domain\ScanAnalysis\MoonScan;
use Domain\ScanAnalysis\Material;

/**
 * Parses moon survey scan data from Eve Online
 *
 * Format: MOON_ID\tMATERIAL_NAME\tQUANTITY\nMOON_ID\tMATERIAL_NAME\tQUANTITY...
 */
class ScanParser
{
    private const MOON_SCAN_PATTERN = '/^(\d+)\t(.+)\t([\d.,]+)\s*$/m';

    /**
     * Parse moon scan data and extract materials
     *
     * @param string $scanData Raw scan text from Eve Online
     * @return MoonScan Parsed scan with materials grouped by moon
     * @throws \InvalidArgumentException if scan data is invalid
     */
    public function parseMoonScan(string $scanData): MoonScan
    {
        $scanData = trim($scanData);
        if (empty($scanData)) {
            throw new \InvalidArgumentException('Scan data cannot be empty');
        }

        $moons = [];
        $matches = [];

        if (!preg_match_all(self::MOON_SCAN_PATTERN, $scanData, $matches, PREG_SET_ORDER)) {
            throw new \InvalidArgumentException('No valid moon scan lines detected. Expected format: MOON_ID\\tMATERIAL\\tQUANTITY');
        }

        foreach ($matches as $match) {
            $moonId = (int) $match[1];
            $materialName = trim($match[2]);
            $quantity = (float) str_replace(',', '', $match[3]);

            if (!isset($moons[$moonId])) {
                $moons[$moonId] = [];
            }

            $moons[$moonId][] = new Material(
                name: $materialName,
                quantity: $quantity
            );
        }

        return new MoonScan(
            moonData: $moons,
            scanTime: new \DateTime('now', new \DateTimeZone('UTC'))
        );
    }

    /**
     * Validate scan format without parsing
     *
     * @param string $scanData
     * @return bool
     */
    public function isValidMoonScan(string $scanData): bool
    {
        return (bool) preg_match(self::MOON_SCAN_PATTERN, $scanData);
    }
}
