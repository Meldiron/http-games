<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->safeLoad();

if (($_ENV['_APP_LOGGING'] ?? 'disabled') === 'disabled') {
    ini_set('error_reporting', 0);
    ini_set('display_errors', 'Off');
    ini_set('display_startup_errors', 'Off');
}

$envVars = [
    '_APP_DATABASE_OVERRIDE',
    '_APP_LOGGING',
    '_APP_APPWRITE_ENDPOINT',
    '_APP_APPWRITE_KEY',
    '_APP_APPWRITE_PROJECT_ID',
    '_APP_DATABASE_OVERRIDE',
];

foreach ($envVars as $envVar) {
    if (! empty(\getenv($envVar))) {
        $_ENV[$envVar] = \getenv($envVar);
    }
}
