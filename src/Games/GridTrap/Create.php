<?php

namespace HTTPGames\Games\GridTrap;

use Utopia\Http\Response;
use Utopia\Platform\Action;

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
