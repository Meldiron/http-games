<?php

namespace HTTPGames\Hooks;

use HTTPGames\Exceptions\HTTPException;
use Utopia\Database\Document;
use Utopia\Platform\Action;

class OnInitWithGridTrapDungeon extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_INIT)
            ->groups(['withGridTrapDungeon'])
            ->inject('user')
            ->inject('gridTrapDungeon')
            ->inject('request')
            ->inject('databaseId')
            ->inject('sdkForTable')
            ->callback($this->action(...));
    }

    public function action(Document $user, Document $dungeon): void
    {
        if ($dungeon->getAttribute('userId') !== $user->getId()) {
            throw new HTTPException(HTTPException::TYPE_FORBIDDEN);
        }
    }
}
