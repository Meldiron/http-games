<?php

namespace HTTPGames\Hooks;

use HTTPGames\Exceptions\HTTPException;
use Utopia\Database\Document;
use Utopia\Platform\Action;

class OnInitWithSession extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_INIT)
            ->groups(['withSession'])
            ->inject('user')
            ->callback($this->action(...));
    }

    public function action(Document $user): void
    {
        if ($user->isEmpty()) {
            throw new HTTPException(HTTPException::TYPE_UNAUTHORIZED);
        }
    }
}
