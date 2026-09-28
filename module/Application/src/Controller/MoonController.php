<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use App\Entity\Moon;
use App\Entity\Scan;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

class MoonController extends AbstractActionController
{
    public function __construct(
        private readonly EntityManager $entityManager
    ) {
    }

    /**
     * List all moons, with optional filtering.
     * Query params: system_id, limit, offset, order_by
     */
    public function listAction(): JsonModel
    {
        try {
            $query = $this->getRequest()->getQuery();
            $limit = (int) ($query->get('limit') ?? 50);
            $offset = (int) ($query->get('offset') ?? 0);
            $systemId = $query->get('system_id');
            $orderBy = $query->get('order_by', 'lastScannedAt'); // or 'name', 'createdAt'

            // Validate limits
            $limit = min($limit, 500);
            $offset = max($offset, 0);

            $queryBuilder = $this->entityManager
                ->createQueryBuilder()
                ->select('m')
                ->from(Moon::class, 'm');

            if ($systemId) {
                $queryBuilder
                    ->where('m.systemId = :systemId')
                    ->setParameter('systemId', (int) $systemId);
            }

            $queryBuilder->orderBy('m.' . $this->sanitizeOrderBy($orderBy), 'DESC');

            // Get total count
            $countQuery = clone $queryBuilder;
            $totalCount = count($countQuery->getQuery()->getResult());

            // Apply pagination
            $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            $moons = $queryBuilder->getQuery()->getResult();

            $moonData = array_map(
                static fn (Moon $moon) => [
                    'eve_id' => $moon->getEveId(),
                    'name' => $moon->getName(),
                    'system_id' => $moon->getSystemId(),
                    'system_name' => $moon->getSystemName(),
                    'planet_name' => $moon->getPlanetName(),
                    'moon_index' => $moon->getMoonIndex(),
                    'owned_by' => $moon->getOwnedBy(),
                    'last_scanned_at' => $moon->getLastScannedAt()?->format('c'),
                    'created_at' => $moon->getCreatedAt()->format('c'),
                    'updated_at' => $moon->getUpdatedAt()->format('c'),
                ],
                $moons
            );

            return new JsonModel([
                'status' => 'success',
                'data' => $moonData,
                'pagination' => [
                    'total' => $totalCount,
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($moonData),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to list moons',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get details for a specific moon including material composition.
     */
    public function detailAction(): JsonModel
    {
        try {
            $moonId = (int) $this->params('id');

            $moon = $this->entityManager
                ->getRepository(Moon::class)
                ->findByEveId($moonId);

            if (!$moon) {
                return new JsonModel([
                    'error' => 'Moon not found',
                    'moon_id' => $moonId,
                ], 404);
            }

            // Get recent material composition
            $moonMaterials = $this->entityManager
                ->createQueryBuilder()
                ->select('mm, m')
                ->from('App\Entity\MoonMaterial', 'mm')
                ->innerJoin('mm.material', 'm')
                ->where('mm.moon = :moon')
                ->setParameter('moon', $moon)
                ->orderBy('mm.scannedAt', 'DESC')
                ->setMaxResults(100)
                ->getQuery()
                ->getResult();

            $materials = array_map(
                static fn ($mm) => [
                    'type_id' => $mm->getMaterial()->getEveTypeId(),
                    'name' => $mm->getMaterial()->getName(),
                    'category' => $mm->getMaterial()->getCategory(),
                    'quantity' => $mm->getQuantity(),
                    'unit_price' => $mm->getMaterial()->getCurrentPrice(),
                    'total_value' => $mm->getTotalValue(),
                    'scanned_at' => $mm->getScannedAt()->format('c'),
                ],
                $moonMaterials
            );

            $totalValue = array_sum(
                array_map(static fn ($m) => $m['total_value'] ?? 0, $materials)
            );

            return new JsonModel([
                'status' => 'success',
                'data' => [
                    'eve_id' => $moon->getEveId(),
                    'name' => $moon->getName(),
                    'system_id' => $moon->getSystemId(),
                    'system_name' => $moon->getSystemName(),
                    'planet_name' => $moon->getPlanetName(),
                    'moon_index' => $moon->getMoonIndex(),
                    'owned_by' => $moon->getOwnedBy(),
                    'notes' => $moon->getNotes(),
                    'materials' => $materials,
                    'total_value_isk' => $totalValue,
                    'material_count' => count($materials),
                    'last_scanned_at' => $moon->getLastScannedAt()?->format('c'),
                    'created_at' => $moon->getCreatedAt()->format('c'),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to fetch moon details',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all moons in a specific system with their total ISK values.
     */
    public function systemAction(): JsonModel
    {
        try {
            $systemId = (int) $this->params('id');
            $query = $this->getRequest()->getQuery();
            $limit = min((int) ($query->get('limit') ?? 100), 500);
            $offset = max((int) ($query->get('offset') ?? 0), 0);

            $moons = $this->entityManager
                ->createQueryBuilder()
                ->select('m')
                ->from(Moon::class, 'm')
                ->where('m.systemId = :systemId')
                ->setParameter('systemId', $systemId)
                ->orderBy('m.lastScannedAt', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();

            $systemData = [];
            $totalSystemValue = 0;

            foreach ($moons as $moon) {
                $moonMaterials = $this->entityManager
                    ->createQueryBuilder()
                    ->select('mm')
                    ->from('App\Entity\MoonMaterial', 'mm')
                    ->where('mm.moon = :moon')
                    ->setParameter('moon', $moon)
                    ->orderBy('mm.scannedAt', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult();

                $moonValue = array_sum(
                    array_map(static fn ($mm) => $mm->getTotalValue() ?? 0, $moonMaterials)
                );
                $totalSystemValue += $moonValue;

                $systemData[] = [
                    'eve_id' => $moon->getEveId(),
                    'name' => $moon->getName(),
                    'owned_by' => $moon->getOwnedBy(),
                    'total_value_isk' => $moonValue,
                    'material_count' => count($moonMaterials),
                    'last_scanned_at' => $moon->getLastScannedAt()?->format('c'),
                ];
            }

            return new JsonModel([
                'status' => 'success',
                'system_id' => $systemId,
                'moons' => $systemData,
                'system_total_value_isk' => $totalSystemValue,
                'moon_count' => count($systemData),
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to fetch system moons',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function sanitizeOrderBy(string $orderBy): string
    {
        $allowed = ['lastScannedAt', 'createdAt', 'name', 'systemId'];
        return in_array($orderBy, $allowed, true) ? $orderBy : 'lastScannedAt';
    }
}
