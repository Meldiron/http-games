<?php

use Appwrite\Services\TablesDB;
use Utopia\Database\Document;

/**
 * @return array<Document> Tables being created
 */
function setupUsers(TablesDB $sdkForTables, string $databaseId): array
{
    $table = $sdkForTables->createTable($databaseId, 'users', 'Users');
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

    return [new Document($table)];
}
