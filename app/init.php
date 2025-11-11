<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->safeLoad();

// Disable logging-to-browser, recommended for non-local environments
ini_set('error_reporting', 0);
ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');

// Merge .env contents and $_ENV
// TODO: Clean this up, you should not need list of all env variables
$envVars = [
    '_APP_DATABASE_OVERRIDE',
    '_APP_APPWRITE_ENDPOINT',
    '_APP_APPWRITE_KEY',
    '_APP_APPWRITE_PROJECT_ID',
];
foreach ($envVars as $envVar) {
    if (! empty(\getenv($envVar))) {
        $_ENV[$envVar] = \getenv($envVar);
    }
}
