<?php

use Appwrite\Services\TablesDB;
use Utopia\Database\Document;

require_once __DIR__.'/grid-trap/tiles.php';
require_once __DIR__.'/grid-trap/dungeons.php';

/**
 * @return array<Document> Tables being created
 */
function setupGridTrap(TablesDB $sdkForTables, string $databaseId): array
{
    $tables = \array_merge(
        [],
        setupGridTrapTiles($sdkForTables, $databaseId),
        setupGridTrapDungeons($sdkForTables, $databaseId)
    );

    return $tables;
}
