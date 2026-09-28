<?php

namespace Application\Service;

use App\Service\ScanParser;

class ScanAnalysisService
{
    public function __construct(
        private readonly ScanParser $scanParser
    ) {
    }

    /**
     * Analyze raw moon scan input and return normalized data.
     *
     * @param string $scanText
     * @return array<string, mixed>
     */
    public function analyze(string $scanText): array
    {
        $scan = $this->scanParser->parseMoonScan($scanText);
        $moonSummaries = [];

        foreach ($scan->getMoonIds() as $moonId) {
            $materials = $scan->getMaterialsForMoon((int) $moonId);
            $moonSummaries[(string) $moonId] = array_map(
                static fn ($material) => [
                    'name' => $material->getName(),
                    'quantity' => $material->getQuantity(),
                ],
                $materials ?? []
            );
        }

        $materialCount = 0;
        foreach ($moonSummaries as $moonMaterials) {
            $materialCount += count($moonMaterials);
        }

        return [
            'moon_count' => $scan->getMoonCount(),
            'material_count' => $materialCount,
            'moons' => $moonSummaries,
        ];
    }
}
