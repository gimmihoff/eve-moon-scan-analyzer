<?php

namespace Application\Controller;

use App\Service\MaterialPriceService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

/**
 * API endpoints for managing material prices.
 */
class PricingController extends AbstractActionController
{
    public function __construct(
        private readonly MaterialPriceService $priceService
    ) {
    }

    /**
     * Update prices for all materials from ESI.
     * POST /api/pricing/update-all
     */
    public function updateAllAction(): JsonModel
    {
        try {
            $updated = $this->priceService->updateAllPrices();

            return new JsonModel([
                'status' => 'success',
                'action' => 'update_all_prices',
                'materials_updated' => $updated,
                'timestamp' => (new \DateTime('now'))->format('c'),
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to update prices',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update price for a specific material.
     * POST /api/pricing/update/:type_id
     */
    public function updateAction(): JsonModel
    {
        try {
            $typeId = (int) $this->params('type_id');

            if ($typeId <= 0) {
                return new JsonModel([
                    'error' => 'Invalid type_id',
                    'type_id' => $typeId,
                ], 400);
            }

            $updated = $this->priceService->updateMaterialPrice($typeId);

            if (!$updated) {
                return new JsonModel([
                    'error' => 'Material not found or price update failed',
                    'type_id' => $typeId,
                ], 404);
            }

            return new JsonModel([
                'status' => 'success',
                'action' => 'update_material_price',
                'type_id' => $typeId,
                'timestamp' => (new \DateTime('now'))->format('c'),
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to update price',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update prices for a batch of materials.
     * POST /api/pricing/update-batch
     * Body: {"type_ids": [123, 456, 789]}
     */
    public function batchUpdateAction(): JsonModel
    {
        try {
            $payload = $this->getRequest()->getContent();
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

            $typeIds = (array) ($data['type_ids'] ?? []);
            if (empty($typeIds)) {
                return new JsonModel([
                    'error' => 'type_ids array is required',
                ], 400);
            }

            // Ensure all type IDs are integers
            $typeIds = array_filter(
                array_map('intval', $typeIds),
                static fn ($id) => $id > 0
            );

            if (empty($typeIds)) {
                return new JsonModel([
                    'error' => 'No valid type IDs provided',
                ], 400);
            }

            $updated = $this->priceService->updateBatchPrices($typeIds);

            return new JsonModel([
                'status' => 'success',
                'action' => 'batch_update_prices',
                'requested_count' => count($typeIds),
                'updated_count' => $updated,
                'timestamp' => (new \DateTime('now'))->format('c'),
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonModel([
                'error' => 'Invalid request body',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Failed to update prices',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
