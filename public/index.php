<?php

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            if ($key !== '') {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

error_reporting(E_ALL);
ini_set('display_errors', (string) (filter_var(getenv('APP_DEBUG') ?: 'true', FILTER_VALIDATE_BOOLEAN) ? '1' : '0'));
date_default_timezone_set('UTC');

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new RuntimeException('Composer dependencies not installed. Run: composer install');
}

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists(\Laminas\Mvc\Application::class)) {
    throw new RuntimeException('Laminas Framework not found. Run: composer install');
}

try {
    $config = require __DIR__ . '/../config/application.config.php';
    $application = \Laminas\Mvc\Application::init($config);
    $application->run();
} catch (\Throwable $e) {
    http_response_code(500);
    error_log($e->__toString());
    echo json_encode(['error' => 'Internal Server Error']);
}
