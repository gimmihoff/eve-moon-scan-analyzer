<?php

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => \Application\Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'api' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api[/:action[/]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => \Application\Controller\ScanController::class,
                        'action' => 'status',
                    ],
                ],
            ],
            'api.moons' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/moons[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '\d+',
                    ],
                    'defaults' => [
                        'controller' => \Application\Controller\MoonController::class,
                        'action' => 'list',
                    ],
                ],
            ],
            'api.scans' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/scans[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '\d+',
                    ],
                    'defaults' => [
                        'controller' => \Application\Controller\ScanHistoryController::class,
                        'action' => 'list',
                    ],
                ],
            ],
            'api.pricing' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/pricing[/:action[/:type_id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'type_id' => '\d+',
                    ],
                    'defaults' => [
                        'controller' => \Application\Controller\PricingController::class,
                        'action' => 'update_all',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            \Application\Controller\IndexController::class => \Laminas\Mvc\Service\InvokableFactory::class,
            \Application\Controller\ScanController::class => \Application\Controller\Factory\ScanControllerFactory::class,
            \Application\Controller\MoonController::class => \Application\Controller\Factory\MoonControllerFactory::class,
            \Application\Controller\ScanHistoryController::class => \Application\Controller\Factory\ScanHistoryControllerFactory::class,
            \Application\Controller\PricingController::class => \Application\Controller\Factory\PricingControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            \Application\Service\ScanAnalysisService::class => \Application\Service\Factory\ScanAnalysisServiceFactory::class,
            \App\Service\ScanPersistenceService::class => \App\Service\Factory\ScanPersistenceServiceFactory::class,
            \App\Service\MaterialPriceService::class => \App\Service\Factory\MaterialPriceServiceFactory::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            'app_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AttributeDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../../src/Application/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'App\\Entity' => 'app_driver',
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
