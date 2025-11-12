<?php

namespace HTTPGames\Games\GridTrap\Dungeons\Actions\Crawl;

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
            ->setHttpPath('/v1/games/grid-trap/dungeons/:dungeonId/actions/crawl')
            ->desc('Crawl back to enterance')
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

        if ($gridTrapDungeon->getAttribute('hardcore') === true) {
            throw new HTTPException(HTTPException::TYPE_HARDCORE_ACTION_NOT_ALLOWED);
        }

        $isTrapped = $gridTrapDungeon->getAttribute('cartographerTrapped');

        if (! $isTrapped) {
            throw new HTTPException(HTTPException::TYPE_CRAWL_NOT_REQUIRED);
        }

        $tiles = $sdkForTables->listRows($databaseId, 'gridTrapTiles', [
            Query::equal('dungeonId', $dungeonId),
            Query::equal('type', 'enterance'),
            Query::limit(1),
        ]);

        $enteranceTile = new Document($tiles['rows'][0] ?? []);

        // Server error
        if ($enteranceTile->isEmpty()) {
            throw new \Exception('Enterance tile could not be found.');
        }

        $updates = [
            'cartographerTrapped' => false,
            'cartographerPosition' => $enteranceTile->getAttribute('position'),
        ];

        $sdkForTables->updateRow($databaseId, 'gridTrapDungeons', $dungeonId, $updates);

        $response->setStatusCode(Response::STATUS_CODE_CREATED);
        $response->json(\array_merge([
            'type' => 'crawl',
        ], $updates));
    }
}
