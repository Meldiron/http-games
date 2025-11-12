<?php

namespace HTTPGames\Games\GridTrap\Dungeons\Tiles;

use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\PointID;
use HTTPGames\Validators\UID;
use Utopia\Platform\Action;
use Utopia\Response;

class Get extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('GET')
            ->setHttpPath('/v1/games/grid-trap/dungeons/:dungeonId/tiles/:tilePoint')
            ->desc('Get info about tile')
            ->groups(['withSession', 'withGridTrapDungeon'])
            ->param('dungeonId', '', new UID)
            ->param('tilePoint', '', new PointID)
            ->inject('response')
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->callback($this->action(...));
    }

    public function action(
        string $dungeonId,
        string $tilePoint,
        Response $response,
        string $databaseId,
        TablesDB $sdkForTables
    ): void {
        $parts = explode('_', $tilePoint);
        $x = \intval($parts[0]);
        $y = \intval($parts[1]);

        $tiles = $sdkForTables->listRows($databaseId, 'gridTrapTiles', [
            Query::distanceEqual('position', [$x, $y], 0),
            Query::equal('dungeonId', $dungeonId),
            Query::limit(1),
        ]);

        if ($tiles['total'] === 0) {
            throw new HTTPException(HTTPException::TYPE_TILE_NOT_FOUND);
        }

        $tile = $tiles['rows'][0];

        $type = $tile['type'];
        if ($type == 'trap') {
            $type = 'ground';
        }

        $response->json([
            'x' => $tile['position'][0],
            'y' => $tile['position'][1],
            'type' => $type,
        ]);
    }
}
