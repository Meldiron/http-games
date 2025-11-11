<?php

use Appwrite\AppwriteException;
use Appwrite\Client;
use Appwrite\Enums\RelationMutate;
use Appwrite\Enums\RelationshipType;
use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use Utopia\App;
use Utopia\Database\Document;
use Utopia\Request;

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

        try {
            // TODO: Optimize for performance on production, those calls should not be needed
            $sdkForTables->get($databaseId);
            $sdkForTables->getTable($databaseId, 'ready001'); // Last rediness check
        } catch (AppwriteException $err) {
            if ($err->getType() === 'database_not_found' || $err->getType() === 'table_not_found' || $err->getCode() === 500) {
                // TODO: This should be elsewhere, to keep resource simple
                // Setup database schema
                $exists = false;
                try {
                    $sdkForTables->create(
                        databaseId: $databaseId,
                        name: $databaseId,
                    );
                } catch (AppwriteException $err) {
                    if ($err->getType() === 'database_already_exists') {
                        $exists = true;
                    } else {
                        throw $err;
                    }
                }

                if (! $exists) {
                    $sdkForTables->createTable($databaseId, 'users', 'Users');
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'users',
                        'email',
                        255,
                        required: true,
                    );
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'users',
                        'passwordHash',
                        255,
                        required: true,
                        encrypt: true,
                    );
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'users',
                        'nickname',
                        255,
                        required: true,
                    );

                    $sdkForTables->createTable($databaseId, 'tokens', 'Tokens');
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'tokens',
                        'userId',
                        255,
                        required: true,
                    );
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'tokens',
                        'secret',
                        255,
                        required: true,
                    );

                    $sdkForTables->createTable(
                        $databaseId,
                        'gridTrapTiles',
                        'Grid Trap - Tiles',
                    );
                    $sdkForTables->createPointColumn(
                        $databaseId,
                        'gridTrapTiles',
                        'position',
                        required: true,
                    );
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'gridTrapTiles',
                        'type',
                        255,
                        required: true,
                    );
                    // dungeonId also available using relationship originated from gridTrapDungeons

                    $sdkForTables->createTable(
                        $databaseId,
                        'gridTrapDungeons',
                        'Grid Trap - Dungeons',
                    );
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'gridTrapDungeons',
                        'userId',
                        255,
                        required: true,
                    );
                    $sdkForTables->createStringColumn(
                        $databaseId,
                        'gridTrapDungeons',
                        'size',
                        15,
                        required: true,
                    );
                    $sdkForTables->createIntegerColumn(
                        $databaseId,
                        'gridTrapDungeons',
                        'seed',
                        required: true,
                    );
                    $sdkForTables->createBooleanColumn(
                        $databaseId,
                        'gridTrapDungeons',
                        'seedCustomized',
                        required: true,
                    );
                    $sdkForTables->createBooleanColumn(
                        $databaseId,
                        'gridTrapDungeons',
                        'hardcore',
                        required: true,
                    );
                    $sdkForTables->createPointColumn(
                        $databaseId,
                        'gridTrapDungeons',
                        'playerPosition',
                        required: true,
                    );
                    $sdkForTables->createBooleanColumn(
                        $databaseId,
                        'gridTrapDungeons',
                        'playerTrapped',
                        required: true,
                    );
                    $sdkForTables->createRelationshipColumn(
                        $databaseId,
                        tableId: 'gridTrapDungeons',
                        relatedTableId: 'gridTrapTiles',
                        type: RelationshipType::ONETOMANY(),
                        twoWay: true,
                        key: 'tiles',
                        twoWayKey: 'dungeonId',
                        onDelete: RelationMutate::CASCADE(),
                    );

                    // Always keep last
                    $sdkForTables->createTable($databaseId, 'ready000', 'Tables are ready');
                }

                $attempt = 0;
                while (true) {
                    $attempt++;
                    if ($attempt > 15) {
                        throw new Exception('Failed to unlock tables.');
                    }

                    try {
                        $sdkForTables->getTable($databaseId, 'ready000');
                        break;
                    } catch (AppwriteException $err) {
                        if ($err->getType() === 'table_not_found' || $err->getCode() === 500) {
                            \sleep(1);

                            continue;
                        }

                        throw $err;
                    }
                }

                // TODO: This list should be automatic
                $tables = ['users', 'tokens', 'gridTrapDungeons', 'gridTrapTiles'];
                $attempts = 0;
                while (true) {
                    $processing = false;
                    foreach ($tables as $table) {
                        try {
                            $rows = $sdkForTables->listColumns(
                                $databaseId,
                                $table,
                                [
                                    Query::notEqual('status', 'available'),
                                    Query::limit(1),
                                ],
                            );

                            if ($rows['total'] > 0) {
                                $processing = true;
                            }
                        } catch (AppwriteException $err) {
                            if ($err->getType() === 'table_not_found' || $err->getCode() === 500) {
                                $processing = true;
                                break;
                            }

                            throw $err;
                        }
                    }

                    if (! $processing) {
                        break;
                    }

                    $attempts++;
                    if ($attempts > 15) {
                        throw new Exception('Database not setup properly.');
                    }

                    \sleep(1);
                }

                try {
                    $sdkForTables->createTable($databaseId, 'ready001', 'Columns are ready');
                } catch (AppwriteException $err) {
                    if ($err->getType() === 'table_already_exists') {
                        // OK
                    } else {
                        throw $err;
                    }
                }

            } else {
                throw $err;
            }
        }

        return $sdkForTables;
    },
    ['sdk', 'databaseId'],
);
