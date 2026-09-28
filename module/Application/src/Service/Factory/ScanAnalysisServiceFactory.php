<?php

namespace Application\Service\Factory;

use Application\Service\ScanAnalysisService;
use App\Service\ScanParser;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ScanAnalysisServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ScanAnalysisService(new ScanParser());
    }
}
