<?php

use Appwrite\Enums\RelationMutate;
use Appwrite\Enums\RelationshipType;
use Appwrite\Services\TablesDB;
use Utopia\Database\Document;

/**
 * @return array<Document> Tables being created
 */
function setupGridTrapDungeons(TablesDB $sdkForTables, string $databaseId): array
{
    $table = $sdkForTables->createTable(
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

    return [new Document($table)];
}
