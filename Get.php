<?php

namespace HTTPGames\Games\GridTrap\Dungeons\Cartographer;

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
            ->setHttpPath('/v1/games/grid-trap/dungeons/:dungeonId/cartographer')
            ->desc('Get cartographer position')
            ->groups(['withSession', 'withGridTrapDungeon'])
            ->param('dungeonId', '', new UID)
            ->inject('gridTrapDungeon')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(string $dungeonId, Document $gridTrapDungeon, Response $response): void
    {
        $dungeon = $gridTrapDungeon;

        if ($dungeon->isEmpty()) {
            throw new HTTPException(HTTPException::TYPE_DUNGEON_NOT_FOUND);
        }

        $response->json([
            'x' => $dungeon['cartographerPosition'][0],
            'y' => $dungeon['cartographerPosition'][1],
            'trapped' => $dungeon['cartographerTrapped'],
        ]);
    }
}
