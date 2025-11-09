<?php

namespace HTTPGames\Events;

use Utopia\Platform\Action;

class OnError extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_ERROR)
            ->groups(['*'])
            ->inject('error')
            ->callback($this->action(...));
    }

    public function action(\Throwable $error): void
    {
        \var_dump($error);
    }
}
