<?php

namespace HTTPGames\Games\GridTrap;

use Utopia\Platform\Action;
use Utopia\Response;

class Create extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('GET')
            ->setHttpPath('/hello')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(Response $response): void
    {
        $response->send('Hello World!');
    }
}
