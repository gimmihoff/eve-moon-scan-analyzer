<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'title' => 'Eve Moon Scan Analyzer',
            'message' => 'The API is ready for moon scan ingestion and analysis.',
        ]);
    }
}
