<?php

namespace Application\Controller\Factory;

use Application\Controller\ScanHistoryController;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ScanHistoryControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ScanHistoryController(
            $container->get(EntityManager::class)
        );
    }
}
