<?php

namespace App\Service;

use App\Entity\Material;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * Keeps material prices synchronized with ESI market data.
 */
class MaterialPriceService
{
    public function __construct(
        private readonly EsiPricingService $esiPricing,
        private readonly EntityManager $entityManager,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    /**
     * Update prices for all materials in the database.
     *
     * @return int Number of materials updated
     */
    public function updateAllPrices(): int
    {
        // Fetch all materials
        $materials = $this->entityManager
            ->getRepository(Material::class)
            ->findAll();

        if (empty($materials)) {
            return 0;
        }

        $typeIds = array_map(
            static fn (Material $m) => $m->getEveTypeId(),
            $materials
        );

        $prices = $this->esiPricing->getPrices($typeIds);
        $updated = 0;

        foreach ($materials as $material) {
            $typeId = $material->getEveTypeId();
            if (isset($prices[$typeId])) {
                $material->setCurrentPrice($prices[$typeId]);
                $this->entityManager->persist($material);
                $updated++;
            }
        }

        $this->entityManager->flush();
        $this->logger?->info('Updated prices for materials', ['count' => $updated]);

        return $updated;
    }

    /**
     * Update price for a specific material.
     *
     * @param int $typeId
     * @return bool True if price was updated
     */
    public function updateMaterialPrice(int $typeId): bool
    {
        $price = $this->esiPricing->getPrice($typeId);
        if ($price === null) {
            return false;
        }

        $material = $this->entityManager
            ->getRepository(Material::class)
            ->findByEveTypeId($typeId);

        if (!$material) {
            return false;
        }

        $material->setCurrentPrice($price);
        $this->entityManager->persist($material);
        $this->entityManager->flush();

        $this->logger?->debug('Updated price for material', ['type_id' => $typeId, 'price' => $price]);

        return true;
    }

    /**
     * Update prices for a batch of materials and recalculate moon values.
     *
     * @param array<int> $typeIds
     * @return int Number of materials updated
     */
    public function updateBatchPrices(array $typeIds): int
    {
        $prices = $this->esiPricing->getPrices($typeIds);
        $updated = 0;

        foreach ($prices as $typeId => $price) {
            $material = $this->entityManager
                ->getRepository(Material::class)
                ->findByEveTypeId($typeId);

            if ($material) {
                $material->setCurrentPrice($price);
                $this->entityManager->persist($material);
                $updated++;
            }
        }

        $this->entityManager->flush();
        $this->logger?->info('Updated batch prices for materials', ['count' => $updated]);

        return $updated;
    }
}
