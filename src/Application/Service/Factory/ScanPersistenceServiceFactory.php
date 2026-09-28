<?php

namespace App\Service\Factory;

use App\Service\ScanPersistenceService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ScanPersistenceServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ScanPersistenceService(
            $container->get(EntityManager::class)
        );
    }
}
