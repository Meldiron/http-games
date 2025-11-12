<?php

use Appwrite\Services\TablesDB;
use Utopia\Database\Document;

/**
 * @return array<Document> Tables being created
 */
function setupGridTrapTiles(TablesDB $sdkForTables, string $databaseId): array
{
    $table = $sdkForTables->createTable(
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
    // "dungeonId" also available using relationship originated from gridTrapDungeons

    return [new Document($table)];
}
