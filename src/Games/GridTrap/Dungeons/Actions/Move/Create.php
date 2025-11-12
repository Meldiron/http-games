<?php

namespace HTTPGames\Games\GridTrap\Dungeons\Actions\Move;

use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\UID;
use Utopia\Database\Document;
use Utopia\Platform\Action;
use Utopia\Response;
use Utopia\Validator\WhiteList;

class Create extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('POST')
            ->setHttpPath('/v1/games/grid-trap/dungeons/:dungeonId/actions/move')
            ->desc('Move to different tile')
            ->groups(['withSession', 'withGridTrapDungeon'])
            ->param('dungeonId', '', new UID)
            ->param('direction', '', new WhiteList(['west', 'north', 'east', 'south']))
            ->inject('gridTrapDungeon')
            ->inject('response')
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->callback($this->action(...));
    }

    public function action(
        string $dungeonId,
        string $direction,
        Document $gridTrapDungeon,
        Response $response,
        string $databaseId,
        TablesDB $sdkForTables
    ): void {
        if ($gridTrapDungeon->getAttribute('status') === 'escaped') {
            throw new HTTPException(HTTPException::TYPE_ACTION_NOT_ALLOWED);
        }

        $isTrapped = $gridTrapDungeon->getAttribute('cartographerTrapped');

        if ($isTrapped) {
            throw new HTTPException(HTTPException::TYPE_MOVE_NOT_ALLOWED);
        }

        $vectors = [
            'west' => [-1, 0],
            'north' => [0, 1],
            'east' => [1, 0],
            'south' => [0, -1],
        ];

        $vector = $vectors[$direction];

        $newPosition = [
            $gridTrapDungeon->getAttribute('cartographerPosition')[0] + $vector[0],
            $gridTrapDungeon->getAttribute('cartographerPosition')[1] + $vector[1],
        ];

        $tiles = $sdkForTables->listRows($databaseId, 'gridTrapTiles', [
            Query::equal('dungeonId', $dungeonId),
            Query::distanceEqual('position', $newPosition, 0),
            Query::limit(1),
        ]);

        $targetTile = new Document($tiles['rows'][0] ?? []);

        // Server error
        if ($targetTile->isEmpty()) {
            throw new \Exception('Target tile could not be found.');
        }

        $collidedWithWall = $targetTile->getAttribute('type') === 'wall';

        if ($collidedWithWall) {
            throw new HTTPException(HTTPException::TYPE_MOVE_TO_WALL_NOT_ALLOWED);
        }

        $updates = [
            'cartographerPosition' => $newPosition,
        ];

        $steppedOnTrap = $targetTile->getAttribute('type') === 'trap';

        if ($steppedOnTrap) {
            $updates['cartographerTrapped'] = true;

            if ($gridTrapDungeon->getAttribute('hardcore')) {
                $updates['status'] = 'trapped';
            }
        }

        $sdkForTables->updateRow($databaseId, 'gridTrapDungeons', $dungeonId, $updates);

        $response->setStatusCode(Response::STATUS_CODE_CREATED);
        $response->json(\array_merge([
            'type' => 'move',
        ], $updates));
    }
}
