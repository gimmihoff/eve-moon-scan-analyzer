<?php

/**
 * Eve Moon Scan Analyzer - Entry Point
 */

chdir(dirname(__DIR__));

// Decline static file requests
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
}

// Load environment variables
if (file_exists('.env')) {
    $dotenv = new \Laminas\Dotenv\Dotenv();
    $dotenv->load('.env');
}

// Set up error handling
error_reporting(-1);
ini_set('display_errors', (int) getenv('APP_DEBUG'));
date_default_timezone_set('UTC');

// Load Composer autoloader
if (!file_exists('vendor/autoload.php')) {
    throw new RuntimeException(
        'Composer dependencies not installed. Run: composer install'
    );
}
require 'vendor/autoload.php';

// Load Laminas application
if (!class_exists(\Laminas\Mvc\Application::class)) {
    throw new RuntimeException(
        'Laminas Framework not found. Run: composer install'
    );
}

// Run the application
try {
    $config = require 'config/application.config.php';
    $application = \Laminas\Mvc\Application::init($config);
    $application->run();
} catch (\Throwable $e) {
    error_log($e->__toString());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}
