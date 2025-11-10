<?php

use Appwrite\AppwriteException;
use Appwrite\Client;
use Appwrite\Query;
use Appwrite\Services\TablesDB;
use Utopia\App;
use Utopia\Database\Document;
use Utopia\Request;

App::setResource('user', function (Request $request, string $databaseId, TablesDB $sdkForTables) {
    // Bearer sk_...
    $authorization = $request->getHeader('authorization', '');
    $key = \explode(' ', $authorization);
    $token = $key[1] ?? '';

    if (empty($token) || ! (\str_starts_with($token, 'sk_'))) {
        return new Document;
    }

    $users = $sdkForTables->listRows(
        databaseId: $databaseId,
        tableId: 'users',
        queries: [
            Query::equal('token', $token),
            Query::limit(1),
        ]
    );

    if ($users['total'] <= 0) {
        return new Document;
    }

    $user = $users['rows'][0];

    return new Document($user);

}, ['request', 'databaseId', 'sdkForTables']);

App::setResource('databaseId', function () {
    $databaseId = $_ENV['_APP_DATABASE_OVERRIDE'];

    if (empty($databaseId)) {
        throw new Exception('Database ID override is currently required.');
    }

    return $databaseId;
});

App::setResource('sdk', function () {
    $sdk = new Client;

    $sdk
        ->setEndpoint($_ENV['_APP_APPWRITE_ENDPOINT'])
        ->setKey($_ENV['_APP_APPWRITE_KEY'])
        ->setProject($_ENV['_APP_APPWRITE_PROJECT_ID']);

    return $sdk;
});

App::setResource('sdkForTables', function (Client $sdk, string $databaseId) {
    $sdkForTables = new TablesDB($sdk);

    try {
        $sdkForTables->get($databaseId);
    } catch (AppwriteException $err) {
        if ($err->getType() === 'database_not_found') {

            // Setup database schema
            $sdkForTables->create(databaseId: $databaseId, name: $databaseId);
            $sdkForTables->createTable($databaseId, 'users', 'Users');
            $sdkForTables->createStringColumn($databaseId, 'users', 'email', 255, required: true);
            $sdkForTables->createStringColumn($databaseId, 'users', 'passwordHash', 255, required: true, encrypt: true);
            $sdkForTables->createStringColumn($databaseId, 'users', 'token', 255, required: true);
            $sdkForTables->createStringColumn($databaseId, 'users', 'nickname', 255, required: true);

            $attempts = 0;
            while (true) {
                $rows = $sdkForTables->listColumns($databaseId, 'users', [
                    Query::notEqual('status', 'available'),
                    Query::limit(1),
                ]);
                if ($rows['total'] === 0) {
                    break;
                }

                $attempts++;
                if ($attempts > 15) {
                    throw new Exception('Database not setup properly.');
                }

                \sleep(1);
            }
        } else {
            throw $err;
        }
    }

    return $sdkForTables;
}, ['sdk', 'databaseId']);
