<?php

// Load environment
if (file_exists(__DIR__ . '/../.env.testing')) {
    $dotenv = new \Laminas\Dotenv\Dotenv();
    $dotenv->load(__DIR__ . '/../.env.testing');
}

// Set up timezone
date_default_timezone_set('UTC');

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';
