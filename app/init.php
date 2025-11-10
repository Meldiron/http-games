<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

if (($_ENV['_APP_LOGGING'] ?? 'disabled') === 'disabled') {
    ini_set('error_reporting', 0);
    ini_set('display_errors', 'Off');
    ini_set('display_startup_errors', 'Off');
}

// Temporary fix for loading .env and env vars together
if (! empty(\getenv('_APP_DATABASE_OVERRIDE'))) {
    $_ENV['_APP_DATABASE_OVERRIDE'] = \getenv('_APP_DATABASE_OVERRIDE');
}
