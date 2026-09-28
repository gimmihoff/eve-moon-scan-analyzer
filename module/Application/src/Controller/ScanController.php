<?php

namespace Application\Controller;

use Application\Service\ScanAnalysisService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

class ScanController extends AbstractActionController
{
    public function __construct(
        private readonly ScanAnalysisService $scanAnalysisService
    ) {
    }

    public function statusAction(): JsonModel
    {
        return new JsonModel([
            'status' => 'ok',
            'service' => 'eve-moon-scan-analyzer',
            'version' => '0.2.0',
            'features' => [
                'moon_scan_parsing',
                'database_persistence',
                'material_tracking',
            ],
        ]);
    }

    public function uploadAction(): JsonModel
    {
        try {
            $payload = $this->getRequest()->getContent();
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

            $scanText = (string) ($data['scan_data'] ?? '');
            if ($scanText === '') {
                return new JsonModel([
                    'error' => 'scan_data is required',
                    'error_code' => 'missing_scan_data',
                ], 400);
            }

            $submittedBy = $data['submitted_by'] ?? null;
            $result = $this->scanAnalysisService->analyze($scanText, $submittedBy);

            return new JsonModel([
                'status' => 'success',
                'scan_id' => $result['scan_id'],
                'moon_count' => $result['moon_count'],
                'material_count' => $result['material_count'],
                'moons' => $result['moons'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonModel([
                'error' => $e->getMessage(),
                'error_code' => 'invalid_scan_format',
            ], 400);
        } catch (\Exception $e) {
            return new JsonModel([
                'error' => 'Internal server error',
                'error_code' => 'server_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
