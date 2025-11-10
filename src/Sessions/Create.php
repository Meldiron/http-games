<?php

namespace HTTPGames\Sessions;

use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\Email;
use Utopia\Platform\Action;
use Utopia\Response;
use Utopia\Validator\Text;

class Create extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('POST')
            ->setHttpPath('/v1/sessions')
            ->desc('Sign in as user')
            ->param('email', '', new Email)
            ->param('password', '', new Text(256, 8))
            ->inject('response')
            ->inject('sdkForTables')
            ->inject('databaseId')
            ->callback($this->action(...));
    }

    public function action(
        string $email,
        string $password,
        Response $response,
        TablesDB $sdkForTables,
        string $databaseId
    ): void {
        $users = $sdkForTables->listRows(
            databaseId: $databaseId,
            tableId: 'users',
            queries: [
                Query::equal('email', $email),
                Query::limit(1),
            ]
        );

        if ($users['total'] <= 0) {
            throw new HTTPException(HTTPException::TYPE_WRONG_CREDENTIALS);
        }

        $user = $users['rows'][0];

        $passwordHash = $user['passwordHash'];

        if (! (\password_verify($password, $passwordHash))) {
            throw new HTTPException(HTTPException::TYPE_WRONG_CREDENTIALS);
        }

        $response->setStatusCode(Response::STATUS_CODE_CREATED);
        $response->json([
            'id' => $user['$id'],
            'email' => $user['email'],
            'nickname' => $user['nickname'],
            'token' => $user['token'],
        ]);
    }
}
