<?php

namespace App\Service\Factory;

use App\Service\EsiPricingService;
use App\Service\MaterialPriceService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Log\LoggerInterface;

class MaterialPriceServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $logger = $container->has(LoggerInterface::class)
            ? $container->get(LoggerInterface::class)
            : null;

        return new MaterialPriceService(
            new EsiPricingService($logger),
            $container->get(EntityManager::class),
            $logger
        );
    }
}
