<?php

return [
    'db' => [
        'driver' => getenv('DB_DRIVER') ?: 'pdo_mysql',
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'eve_scanner',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'app' => [
        'name' => getenv('APP_NAME') ?: 'Eve Moon Scan Analyzer',
        'env' => getenv('APP_ENV') ?: 'development',
        'debug' => filter_var(getenv('APP_DEBUG') ?: 'true', FILTER_VALIDATE_BOOLEAN),
    ],
    'esi' => [
        'datasource' => getenv('ESI_DATASOURCE') ?: 'tranquility',
        'client_id' => getenv('ESI_CLIENT_ID') ?: '',
        'secret_key' => getenv('ESI_SECRET_KEY') ?: '',
        'callback_url' => getenv('ESI_CALLBACK_URL') ?: 'http://localhost:8080/auth/callback',
    ],
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: 'dev-secret-change-me',
        'algorithm' => getenv('JWT_ALGORITHM') ?: 'HS256',
        'expiry' => (int) (getenv('JWT_EXPIRY') ?: 3600),
    ],
];
