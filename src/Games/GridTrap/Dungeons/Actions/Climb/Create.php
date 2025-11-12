<?php

namespace HTTPGames\Games\GridTrap\Dungeons\Actions\Climb;

use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\UID;
use Utopia\Database\Document;
use Utopia\Platform\Action;
use Utopia\Response;

class Create extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('POST')
            ->setHttpPath('/v1/games/grid-trap/dungeons/:dungeonId/actions/climb')
            ->desc('Climb rope to escape')
            ->groups(['withSession', 'withGridTrapDungeon'])
            ->param('dungeonId', '', new UID)
            ->inject('gridTrapDungeon')
            ->inject('response')
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->callback($this->action(...));
    }

    public function action(
        string $dungeonId,
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
            throw new HTTPException(HTTPException::TYPE_CLIMB_NOT_ALLOWED);
        }

        $tiles = $sdkForTables->listRows($databaseId, 'gridTrapTiles', [
            Query::equal('dungeonId', $dungeonId),
            Query::equal('type', 'rope'),
            Query::limit(1),
        ]);

        $ropeTile = new Document($tiles['rows'][0] ?? []);

        // Server error
        if ($ropeTile->isEmpty()) {
            throw new \Exception('Rope tile could not be found.');
        }

        $ropePosition = $ropeTile->getAttribute('position');
        $cartographerPosition = $gridTrapDungeon->getAttribute('cartographerPosition');

        if (
            $ropePosition[0] !== $cartographerPosition[0] ||
            $ropePosition[1] !== $cartographerPosition[1]
        ) {
            throw new HTTPException(HTTPException::TYPE_CLIMB_NOT_ALLOWED);
        }

        $updates = [
            'status' => 'escaped',
        ];

        $sdkForTables->updateRow($databaseId, 'gridTrapDungeons', $dungeonId, $updates);

        $response->setStatusCode(Response::STATUS_CODE_CREATED);
        $response->json(\array_merge([
            'type' => 'climb',
        ], $updates));

    }
}
