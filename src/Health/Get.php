<?php

namespace HTTPGames\Health;

use Utopia\Platform\Action;
use Utopia\Response;

class Get extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('GET')
            ->setHttpPath('/v1/health')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(Response $response): void
    {
        $response->json([
            'status' => 'ok',
        ]);
    }
}
