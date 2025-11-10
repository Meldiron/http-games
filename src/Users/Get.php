<?php

namespace HTTPGames\Users;

use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\UID;
use Utopia\Database\Document;
use Utopia\Platform\Action;
use Utopia\Response;

class Get extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('GET')
            ->setHttpPath('/v1/users/:userId')
            ->groups(['withSession'])
            ->desc('Get user details')
            ->param('userId', '', new UID)
            ->inject('response')
            ->inject('user')
            ->callback($this->action(...));
    }

    public function action(
        string $userId,
        Response $response,
        Document $user,
    ): void {
        if ($userId !== $user->getId()) {
            throw new HTTPException(HTTPException::TYPE_FORBIDDEN);
        }

        $response->json([
            'id' => $user->getId(),
            'email' => $user->getAttribute('email'),
            'nickname' => $user->getAttribute('nickname'),
            'token' => $user->getAttribute('token'),
        ]);
    }
}
