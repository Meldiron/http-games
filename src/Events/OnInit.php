<?php

namespace HTTPGames\Events;

use Utopia\Platform\Action;

class OnInit extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_INIT)
            ->callback($this->action(...));
    }

    public function action(): void
    {
        \var_dump('On init');
    }
}
