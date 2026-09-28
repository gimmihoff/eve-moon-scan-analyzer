<?php

namespace Application\Controller\Factory;

use Application\Controller\ScanController;
use Application\Service\ScanAnalysisService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ScanControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ScanController(
            $container->get(ScanAnalysisService::class)
        );
    }
}
