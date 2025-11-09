<?php

namespace HTTPGames\Events;

use Utopia\Platform\Action;

class OnShutdown extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_SHUTDOWN)
            ->groups(['*'])
            ->callback($this->action(...));
    }

    public function action(): void
    {
    }
}
