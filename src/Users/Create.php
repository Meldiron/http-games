<?php

namespace HTTPGames\Users;

use Appwrite\ID;
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
            ->setHttpPath('/v1/users')
            ->desc('Register new user')
            ->param('email', '', new Email)
            ->param('password', '', new Text(256, 8))
            ->param('passwordConfirmation', '', new Text(256, 8))
            ->param('nickname', '', new Text(32, 5))
            ->inject('response')
            ->inject('sdkForTables')
            ->inject('databaseId')
            ->callback($this->action(...));
    }

    public function action(
        string $email,
        string $password,
        string $passwordConfirmation,
        string $nickname,
        Response $response,
        TablesDB $sdkForTables,
        string $databaseId
    ): void {
        if ($password !== $passwordConfirmation) {
            throw new HTTPException(HTTPException::TYPE_PASSWORDS_DO_NOT_MATCH);
        }

        $users = $sdkForTables->listRows(
            databaseId: $databaseId,
            tableId: 'users',
            queries: [
                Query::or([
                    Query::equal('email', $email),
                    Query::equal('nickname', $nickname),
                ]),
                Query::limit(1),
            ]
        );

        if ($users['total'] > 0) {
            throw new HTTPException(HTTPException::TYPE_USER_ALREADY_EXISTS);
        }

        $token = 'sk_'.ID::unique(64);

        $user = $sdkForTables->createRow(
            databaseId: $databaseId,
            tableId: 'users',
            rowId: ID::unique(),
            data: [
                'nickname' => $nickname,
                'email' => $email,
                'passwordHash' => \password_hash($password, PASSWORD_ARGON2I),
                'token' => $token,
            ]
        );

        $response->json([
            'id' => $user['$id'],
            'email' => $user['email'],
            'nickname' => $user['nickname'],
            'token' => $user['token'],
        ]);
    }
}
