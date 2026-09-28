<?php

namespace Application\Controller;

use Application\Service\ScanAnalysisService;
use App\Service\ScanParser;
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
            'version' => '0.1.0',
            'features' => [
                'moon_scan_parsing',
                'market_value_estimation',
                'scan_export',
            ],
        ]);
    }

    public function uploadAction(): JsonModel
    {
        $payload = $this->getRequest()->getContent();
        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        $scanText = (string) ($data['scan_data'] ?? '');
        if ($scanText === '') {
            return new JsonModel([
                'error' => 'scan_data is required',
            ]); 
        }

        $result = $this->scanAnalysisService->analyze($scanText);

        return new JsonModel([
            'status' => 'accepted',
            'moon_count' => $result['moon_count'],
            'material_count' => $result['material_count'],
            'moons' => $result['moons'],
        ]);
    }
}
