<?php

namespace Application\Service\Factory;

use Application\Service\EveAuthService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class EveAuthServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new EveAuthService();
    }
}
