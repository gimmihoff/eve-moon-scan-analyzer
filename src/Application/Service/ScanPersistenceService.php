<?php

namespace App\Service;

use App\Entity\Moon;
use App\Entity\Material;
use App\Entity\MoonMaterial;
use App\Entity\Scan;
use Doctrine\ORM\EntityManager;
use Domain\ScanAnalysis\MoonScan;

/**
 * Persists parsed moon scan data to the database.
 */
class ScanPersistenceService
{
    public function __construct(
        private readonly EntityManager $entityManager
    ) {
    }

    /**
     * Store parsed scan data to database.
     *
     * @param MoonScan $moonScan
     * @param string $rawData
     * @param string|null $submittedBy
     * @return Scan
     */
    public function persistScan(
        MoonScan $moonScan,
        string $rawData,
        ?string $submittedBy = null
    ): Scan {
        $materialCount = 0;
        $moonMaterials = [];

        foreach ($moonScan->getMoonIds() as $moonId) {
            $moon = $this->getOrCreateMoon((int) $moonId);
            $materials = $moonScan->getMaterialsForMoon((int) $moonId);

            foreach ($materials ?? [] as $parsedMaterial) {
                $material = $this->getOrCreateMaterial(
                    $parsedMaterial->getName()
                );

                $moonMaterial = new MoonMaterial(
                    $moon,
                    $material,
                    $parsedMaterial->getQuantity()
                );
                $moonMaterial->calculateTotalValue();

                $this->entityManager->persist($moonMaterial);
                $moonMaterials[] = $moonMaterial;
                $materialCount++;
            }

            $moon->setLastScannedAt(new \DateTime('now'));
            $this->entityManager->persist($moon);
        }

        $scan = new Scan(
            'moon_survey',
            $rawData,
            $moonScan->getMoonCount(),
            $materialCount
        );

        if ($submittedBy) {
            $scan->setSubmittedBy($submittedBy);
        }

        $this->entityManager->persist($scan);
        $this->entityManager->flush();

        return $scan;
    }

    private function getOrCreateMoon(int $eveId): Moon
    {
        $moon = $this->entityManager
            ->getRepository(Moon::class)
            ->findByEveId($eveId);

        if (!$moon) {
            $moon = new Moon($eveId);
            $this->entityManager->persist($moon);
            $this->entityManager->flush();
        }

        return $moon;
    }

    private function getOrCreateMaterial(string $name): Material
    {
        $material = $this->entityManager
            ->getRepository(Material::class)
            ->findByName($name);

        if (!$material) {
            // Generate a fake type ID based on name hash for demo purposes
            $typeId = abs(crc32($name)) % 1000000;
            $material = new Material($typeId, $name);
            $this->entityManager->persist($material);
            $this->entityManager->flush();
        }

        return $material;
    }
}
