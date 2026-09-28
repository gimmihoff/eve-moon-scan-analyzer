<?php

namespace Application\Controller\Factory;

use Application\Controller\AuthController;
use Application\Service\EveAuthService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new AuthController(
            new EveAuthService()
        );
    }
}
