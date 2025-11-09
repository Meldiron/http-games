<?php

namespace HTTPGames\Hooks;

use Utopia\Platform\Action;

class OnInit extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_INIT)
            ->groups(['*'])
            ->callback($this->action(...));
    }

    public function action(): void {}
}
