<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->safeLoad();

// Disable logging-to-browser, recommended for non-local environments
ini_set('error_reporting', 0);
ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
