<?php

use Appwrite\AppwriteException;
use Appwrite\Client;
use Appwrite\Services\TablesDB;
use Utopia\App;
use Utopia\CLI\Console;

App::setResource('sdk', function () {
    $sdk = new Client;

    $sdk
        ->setEndpoint($_ENV['_APP_APPWRITE_ENDPOINT'])
        ->setKey($_ENV['_APP_APPWRITE_KEY'])
        ->setProject($_ENV['_APP_APPWRITE_PROJECT_ID']);

    return $sdk;
});

App::setResource('sdkForTables', function (Client $sdk, string $databaseId) {
    $tables = new TablesDB($sdk);

    try {
        $tables->get($databaseId);
    } catch (AppwriteException $err) {
        if ($err->getType() === 'database_not_found') {
            // Create database using Appwrite CLI
            $bash = <<<BASH
                mkdir -p /tmp/$databaseId
                cp ./appwrite.config.json /tmp/$databaseId/appwrite.config.json
                cd /tmp/$databaseId
                sed -i '' 's/production/$databaseId/g' appwrite.config.json
                appwrite push tables --all
            BASH;

            $stdout = '';
            $stderr = '';
            $exitCode = Console::execute($bash, '', $stdout, $stderr, 15);

            if ($exitCode !== 0 || ! empty($stderr)) {
                throw new Exception('Failed to create database with exit code '.$exitCode.': '.$stderr.' ('.$stdout.')');
            }
        } else {
            throw $err;
        }
    }

    return $tables;
}, ['sdk', 'databaseId']);

App::setResource('databaseId', function (Client $sdk) {
    $databaseId = $_ENV['_APP_MODE'];

    if ($databaseId !== 'production') {

        $idOverride = $_ENV['_APP_DATABASE_OVERRIDE'];
        if (empty($idOverride)) {
            $databaseId = 'local_'.date('YmdHis');
        } else {
            $databaseId = $idOverride;
        }
    }

    return $databaseId;
}, ['sdk']);
