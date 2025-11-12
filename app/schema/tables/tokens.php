<?php

use Appwrite\Services\TablesDB;
use Utopia\Database\Document;

/**
 * @return array<Document> Tables being created
 */
function setupTokens(TablesDB $sdkForTables, string $databaseId): array
{
    $table = $sdkForTables->createTable($databaseId, 'tokens', 'Tokens');
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

    return [new Document($table)];
}
