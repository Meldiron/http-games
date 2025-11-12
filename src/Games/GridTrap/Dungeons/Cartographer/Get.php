<?php

namespace HTTPGames\Games\GridTrap\Dungeons\Cartographer;

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
            ->desc('Get cartographer info')
            ->groups(['withSession', 'withGridTrapDungeon'])
            ->param('dungeonId', '', new UID)
            ->inject('gridTrapDungeon')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(string $dungeonId, Document $gridTrapDungeon, Response $response): void
    {
        $response->json([
            'x' => $gridTrapDungeon['cartographerPosition'][0],
            'y' => $gridTrapDungeon['cartographerPosition'][1],
            'trapped' => $gridTrapDungeon['cartographerTrapped'],
        ]);
    }
}
