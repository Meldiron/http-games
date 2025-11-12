<?php

use Appwrite\AppwriteException;
use Appwrite\Client;
use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use Utopia\App;
use Utopia\Database\Document;
use Utopia\Request;

require_once __DIR__.'/schema/setup.php';

App::setResource(
    'user',
    function (Request $request, string $databaseId, TablesDB $sdkForTables) {
        // Bearer sk_...
        $authorization = $request->getHeader('authorization', '');
        $key = \explode(' ', $authorization, 2);
        $token = $key[1] ?? '';

        if (empty($token) || ! \str_starts_with($token, 'sk_')) {
            return new Document;
        }

        $tokens = $sdkForTables->listRows(
            databaseId: $databaseId,
            tableId: 'tokens',
            queries: [Query::equal('secret', $token), Query::limit(1)],
        );

        if ($tokens['total'] <= 0) {
            return new Document;
        }

        $token = $tokens['rows'][0];
        $userId = $token['userId'] ?? '';

        try {
            $user = $sdkForTables->getRow(
                databaseId: $databaseId,
                tableId: 'users',
                rowId: $userId,
            );
        } catch (AppwriteException $err) {
            // TODO: TO be manually tested
            if ($err->getType() === 'row_not_found') {
                throw new HTTPException(HTTPException::TYPE_USER_NOT_FOUND);
            }
            throw $err;
        }

        return new Document($user);
    },
    ['request', 'databaseId', 'sdkForTables'],
);

App::setResource('databaseId', function () {
    $databaseId = $_ENV['_APP_DATABASE_OVERRIDE'];

    if (empty($databaseId)) {
        throw new Exception('Database ID override is currently required.');
    }

    return $databaseId;
});

App::setResource('sdk', function () {
    $sdk = new Client;

    $sdk->setEndpoint($_ENV['_APP_APPWRITE_ENDPOINT'])
        ->setKey($_ENV['_APP_APPWRITE_KEY'])
        ->setProject($_ENV['_APP_APPWRITE_PROJECT_ID']);

    return $sdk;
});

App::setResource(
    'sdkForTables',
    function (Client $sdk, string $databaseId) {
        $sdkForTables = new TablesDB($sdk);

        setupSchema($sdkForTables, $databaseId);

        return $sdkForTables;
    },
    ['sdk', 'databaseId'],
);
