<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

if ($_ENV['_APP_MODE'] === 'production') {
    ini_set('error_reporting', 0);
    ini_set('display_errors', 'Off');
    ini_set('display_startup_errors', 'Off');
}
