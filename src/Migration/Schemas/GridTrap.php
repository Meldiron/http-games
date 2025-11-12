<?php

namespace HTTPGames\Migration\Schemas;

use Appwrite\Enums\RelationMutate;
use Appwrite\Enums\RelationshipType;
use HTTPGames\Migration\Schema;

class GridTrap extends Schema
{
    public function apply(): void
    {
        $this->applyTiles();
        $this->applyDungeons();
    }

    protected function applyTiles(): void
    {
        $this->sdkForTables->createTable(
            $this->databaseId,
            'gridTrapTiles',
            'Grid Trap - Tiles',
        );
        $this->sdkForTables->createPointColumn(
            $this->databaseId,
            'gridTrapTiles',
            'position',
            required: true,
        );
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'gridTrapTiles',
            'type',
            255,
            required: true,
        );
        // "dungeonId" also available using relationship originated from gridTrapDungeons
    }

    protected function applyDungeons(): void
    {
        $this->sdkForTables->createTable(
            $this->databaseId,
            'gridTrapDungeons',
            'Grid Trap - Dungeons',
        );
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'userId',
            255,
            required: true,
        );
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'status',
            255, // started escaped
            required: true,
        );
        $this->sdkForTables->createStringColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'size',
            15,
            required: true,
        );
        $this->sdkForTables->createIntegerColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'seed',
            required: true,
        );
        $this->sdkForTables->createBooleanColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'seedCustomized',
            required: true,
        );
        $this->sdkForTables->createBooleanColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'hardcore',
            required: true,
        );
        $this->sdkForTables->createPointColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'cartographerPosition',
            required: true,
        );
        $this->sdkForTables->createBooleanColumn(
            $this->databaseId,
            'gridTrapDungeons',
            'cartographerTrapped',
            required: true,
        );
        $this->sdkForTables->createRelationshipColumn(
            $this->databaseId,
            tableId: 'gridTrapDungeons',
            relatedTableId: 'gridTrapTiles',
            type: RelationshipType::ONETOMANY(),
            twoWay: true,
            key: 'tiles',
            twoWayKey: 'dungeonId',
            onDelete: RelationMutate::CASCADE(),
        );
    }
}
