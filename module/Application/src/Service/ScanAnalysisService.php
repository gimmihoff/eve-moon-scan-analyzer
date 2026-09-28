<?php

namespace Application\Service;

use App\Service\ScanParser;
use App\Service\ScanPersistenceService;

/**
 * Orchestrates scan parsing and persistence.
 */
class ScanAnalysisService
{
    public function __construct(
        private readonly ScanParser $scanParser,
        private readonly ScanPersistenceService $persistenceService
    ) {
    }

    /**
     * Analyze raw moon scan input, persist to DB, and return normalized data.
     *
     * @param string $scanText
     * @param string|null $submittedBy
     * @return array<string, mixed>
     */
    public function analyze(string $scanText, ?string $submittedBy = null): array
    {
        $moonScan = $this->scanParser->parseMoonScan($scanText);
        $scan = $this->persistenceService->persistScan($moonScan, $scanText, $submittedBy);

        $moonSummaries = [];
        foreach ($moonScan->getMoonIds() as $moonId) {
            $materials = $moonScan->getMaterialsForMoon((int) $moonId);
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
            'scan_id' => $scan->getId(),
            'moon_count' => $scan->getMoonCount(),
            'material_count' => $scan->getMaterialCount(),
            'status' => $scan->getStatus(),
            'moons' => $moonSummaries,
        ];
    }
}
