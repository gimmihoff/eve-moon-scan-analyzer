<?php

namespace Application\Controller\Factory;

use Application\Controller\MoonController;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MoonControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new MoonController(
            $container->get(EntityManager::class)
        );
    }
}
