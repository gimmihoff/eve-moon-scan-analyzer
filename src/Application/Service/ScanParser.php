<?php

namespace App\Service;

use Domain\ScanAnalysis\MoonScan;
use Domain\ScanAnalysis\Material;

/**
 * Parses moon survey scan data from Eve Online.
 *
 * Format: MOON_ID\tMATERIAL_NAME\tQUANTITY\nMOON_ID\tMATERIAL_NAME\tQUANTITY...
 */
class ScanParser
{
    private const MOON_SCAN_PATTERN = '/^(\d+)\t(.+)\t([\d.,]+)\s*$/m';

    public function parseMoonScan(string $scanData): MoonScan
    {
        $scanData = trim($scanData);
        if ($scanData === '') {
            throw new \InvalidArgumentException('Scan data cannot be empty');
        }

        $matches = [];
        if (!preg_match_all(self::MOON_SCAN_PATTERN, $scanData, $matches, PREG_SET_ORDER)) {
            throw new \InvalidArgumentException(
                'No valid moon scan lines detected. Expected format: MOON_ID\tMATERIAL\tQUANTITY'
            );
        }

        $moons = [];
        foreach ($matches as $match) {
            $moonId = (int) $match[1];
            $materialName = trim($match[2]);
            $quantity = (float) str_replace(',', '', $match[3]);

            $moons[$moonId][] = new Material($materialName, $quantity);
        }

        return new MoonScan($moons, new \DateTime('now', new \DateTimeZone('UTC')));
    }

    public function isValidMoonScan(string $scanData): bool
    {
        return (bool) preg_match(self::MOON_SCAN_PATTERN, $scanData);
    }
}
