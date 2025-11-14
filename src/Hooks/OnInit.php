<?php

namespace HTTPGames\Hooks;

use Appwrite\Client;
use HTTPGames\Exceptions\HTTPException;
use Utopia\Abuse\Abuse;
use Utopia\Abuse\Adapters\TimeLimit\Appwrite\TablesDB;
use Utopia\Platform\Action;
use Utopia\Request;

class OnInit extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_INIT)
            ->groups(['*'])
            ->inject('request')
            ->inject('sdk')
            ->inject('databaseId')
            ->callback($this->action(...));
    }

    public function action(Request $request, Client $sdk, string $databaseId): void
    {
        if (($_ENV['_APP_ABUSE'] ?? '') === 'disabled') {
            return;
        }

        $ip = $request->getIP();

        $token = $request->getHeader('authorization', '');
        $token = \explode(' ', $token)[1] ?? '';

        $key = ! empty($token) ? $token : $ip;

        $adapter = new TablesDB($key, limit: 1, seconds: 1, client: $sdk, databaseId: $databaseId.'-abuse');

        $adapter->setup();

        $abuse = new Abuse($adapter);

        if ($abuse->check()) {
            throw new HTTPException(HTTPException::TYPE_RATE_LIMIT_EXCEEDED);
        }
    }
}
