<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'title' => 'Eve Moon Scan Analyzer',
            'message' => 'Paste a moon survey and analyze the material value.'
        ]);
    }
}
