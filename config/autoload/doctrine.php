<?php

return [
    'db' => [
        'driver' => 'Pdo_Mysql',
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'eve_scanner',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => \Doctrine\DBAL\Driver\PDO\MySQL\Driver::class,
                'params' => [
                    'host' => getenv('DB_HOST') ?: 'localhost',
                    'port' => getenv('DB_PORT') ?: '3306',
                    'user' => getenv('DB_USER') ?: 'root',
                    'password' => getenv('DB_PASSWORD') ?: '',
                    'dbname' => getenv('DB_NAME') ?: 'eve_scanner',
                    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => \Doctrine\ORM\Mapping\Driver\DriverChain::class,
                'drivers' => [
                    'App\\Entity' => 'app_driver',
                ],
            ],
            'app_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AttributeDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../../src/Application/Entity'],
            ],
        ],
    ],
];
