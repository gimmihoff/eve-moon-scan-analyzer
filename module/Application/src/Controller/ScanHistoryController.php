<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use App\Entity\Scan;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

class ScanHistoryController extends AbstractActionController
{
    public function __construct(
        private readonly EntityManager $entityManager
    ) {
    }

    /**
     * List all scans with optional filtering.
     * Query params: limit, offset, status, submitted_by, order_by
     */
    public function listAction(): JsonModel
    {
        try {
            $query = $this->getRequest()->getQuery();
            $limit = min((int) ($query->get('limit') ?? 50), 500);
            $offset = max((int) ($query->get('offset') ?? 0), 0);
            $status = $query->get('status');
            $submittedBy = $query->get('submitted_by');
            $orderBy = $query->get('order_by', 'createdAt'); // or 'moonCount', 'materialCount'

            $queryBuilder = $this->entityManager
                ->createQueryBuilder()
                ->select('s')
                ->from(Scan::class, 's');

            if ($status) {
                $queryBuilder
                    ->where('s.status = :status')
                    ->setParameter('status', $status);
            }

            if ($submittedBy) {
                $queryBuilder
                    ->andWhere('s.submittedBy = :submittedBy')
                    ->setParameter('submittedBy', $submittedBy);
            }

            $queryBuilder->orderBy('s.' . $this->sanitizeOrderBy($orderBy), 'DESC');

            // Get total count
            $countQuery = clone $queryBuilder;
            $totalCount = count($countQuery->getQuery()->getResult());

            // Apply pagination
            $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            $scans = $queryBuilder->getQuery()->getResult();

            $scanData = array_map(
                static fn (Scan $scan) => [
                    'id' => $scan->getId(),
                    'scan_type' => $scan->getScanType(),
                    'moon_count' => $scan->getMoonCount(),
                    'material_count' => $scan->getMaterialCount(),
                    'status' => $scan->getStatus(),
                    'submitted_by' => $scan->getSubmittedBy(),
                    'created_at' => $scan->getCreatedAt()->format('c'),
                    'error_message' => $scan->getErrorMessage(),
                ],
                $scans
            );

            return new JsonModel([
                'status' => 'success',
                'data' => $scanData,
                'pagination' => [
                    'total' => $totalCount,
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($scanData),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to list scans',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get details for a specific scan.
     */
    public function detailAction(): JsonModel
    {
        try {
            $scanId = (int) $this->params('id');

            $scan = $this->entityManager
                ->getRepository(Scan::class)
                ->find($scanId);

            if (!$scan) {
                return new JsonModel([
                    'error' => 'Scan not found',
                    'scan_id' => $scanId,
                ], 404);
            }

            return new JsonModel([
                'status' => 'success',
                'data' => [
                    'id' => $scan->getId(),
                    'scan_type' => $scan->getScanType(),
                    'moon_count' => $scan->getMoonCount(),
                    'material_count' => $scan->getMaterialCount(),
                    'status' => $scan->getStatus(),
                    'submitted_by' => $scan->getSubmittedBy(),
                    'created_at' => $scan->getCreatedAt()->format('c'),
                    'error_message' => $scan->getErrorMessage(),
                    'raw_data' => $scan->getRawData(),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to fetch scan details',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get scan statistics (count by status, date range, etc).
     */
    public function statsAction(): JsonModel
    {
        try {
            $query = $this->getRequest()->getQuery();
            $days = (int) ($query->get('days') ?? 30);
            $since = new \DateTime("-{$days} days");

            // Total scans in time period
            $totalScans = $this->entityManager
                ->createQueryBuilder()
                ->select('COUNT(s.id)')
                ->from(Scan::class, 's')
                ->where('s.createdAt >= :since')
                ->setParameter('since', $since)
                ->getQuery()
                ->getSingleScalarResult();

            // Scans by status
            $byStatus = $this->entityManager
                ->createQueryBuilder()
                ->select('s.status, COUNT(s.id) as count')
                ->from(Scan::class, 's')
                ->where('s.createdAt >= :since')
                ->setParameter('since', $since)
                ->groupBy('s.status')
                ->getQuery()
                ->getResult();

            // Total moons and materials processed
            $totals = $this->entityManager
                ->createQueryBuilder()
                ->select('SUM(s.moonCount) as total_moons, SUM(s.materialCount) as total_materials')
                ->from(Scan::class, 's')
                ->where('s.createdAt >= :since')
                ->setParameter('since', $since)
                ->getQuery()
                ->getOneOrNullResult();

            return new JsonModel([
                'status' => 'success',
                'period_days' => $days,
                'since' => $since->format('c'),
                'stats' => [
                    'total_scans' => (int) $totalScans,
                    'by_status' => array_map(
                        static fn ($row) => [
                            'status' => $row['status'] ?? 'null',
                            'count' => (int) $row['count'],
                        ],
                        $byStatus
                    ),
                    'total_moons_processed' => (int) ($totals['total_moons'] ?? 0),
                    'total_materials_processed' => (int) ($totals['total_materials'] ?? 0),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to fetch scan statistics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function sanitizeOrderBy(string $orderBy): string
    {
        $allowed = ['createdAt', 'moonCount', 'materialCount', 'status'];
        return in_array($orderBy, $allowed, true) ? $orderBy : 'createdAt';
    }
}
