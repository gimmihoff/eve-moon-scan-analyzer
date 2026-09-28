<?php

return [
    'modules' => [
        'Laminas\Router',
        'Laminas\Validator',
        'Laminas\Session',
        'Laminas\Authentication',
        'Laminas\PermissionsRbac',
        'Laminas\Log',
        'Laminas\Cache',
        'DoctrineModule',
        'DoctrineORMModule',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => [
            realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php',
        ],
        'config_cache_enabled' => (getenv('APP_ENV') === 'production'),
        'config_cache_key' => 'application.config.cache',
        'module_map_cache_enabled' => (getenv('APP_ENV') === 'production'),
        'module_map_cache_key' => 'application.module.cache',
        'cache_dir' => 'data/cache/',
    ],
];
