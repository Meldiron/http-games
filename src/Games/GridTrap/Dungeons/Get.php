<?php

namespace HTTPGames\Games\GridTrap\Dungeons;

use Appwrite\AppwriteException;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\UID;
use Utopia\Database\Document;
use Utopia\Platform\Action;
use Utopia\Response;

class Get extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('GET')
            ->setHttpPath('/v1/games/grid-trap/dungeons/:dungeonId')
            ->groups(['withSession'])
            ->param('dungeonId', '', new UID)
            ->inject('user')
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(string $dungeonId, Document $user, string $databaseId, TablesDB $sdkForTables, Response $response): void
    {
        try {
            $dungeon = $sdkForTables->getRow($databaseId, 'gridTrapDungeons', $dungeonId);
        } catch (AppwriteException $err) {
            if ($err->getType() === 'row_not_found') {
                throw new HTTPException(HTTPException::TYPE_DUNGEON_NOT_FOUND);
            }
            throw $err;
        }

        if ($dungeon['userId'] !== $user->getId()) {
            throw new HTTPException(HTTPException::TYPE_FORBIDDEN);
        }

        $response->json([
            'id' => $dungeon['$id'],
            'size' => $dungeon['size'],
            'hardcore' => $dungeon['hardcore'],
            'seed' => $dungeon['seed'],
            'status' => $dungeon['status'],
        ]);
    }
}
