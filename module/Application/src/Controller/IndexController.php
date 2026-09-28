<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction(): ViewModel
    {
        $user = $_SESSION['eve_user'] ?? null;

        return new ViewModel([
            'title' => 'Eve Moon Scan Analyzer',
            'message' => 'Paste a moon survey and analyze the material value.',
            'user' => $user,
            'auth_error' => $_SESSION['auth_error'] ?? null,
        ]);
    }
}
