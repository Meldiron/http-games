<?php

namespace HTTPGames\Users;

use Appwrite\ID;
use Appwrite\Query;
use Appwrite\Services\TablesDB;
use Appwrite\Services\Users;
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
            ->inject('sdkForUsers')
            ->inject('sdkForTables')
            ->callback($this->action(...));
    }

    public function action(
        string $email,
        string $password,
        string $passwordConfirmation,
        string $nickname,
        Response $response,
        Users $sdkForUsers,
        TablesDB $sdkForTables
    ): void {
        if ($password !== $passwordConfirmation) {
            throw new HTTPException(HTTPException::TYPE_PASSWORDS_DO_NOT_MATCH);
        }

        // TODO: Rework, only use sdkForTables

        $profiles = $sdkForTables->listRows(
            databaseId: 'main',
            tableId: 'profiles',
            queries: [
                Query::equal('nickname', $nickname),
                Query::limit(1),
            ]
        );

        if ($profiles['total'] > 0) {
            throw new HTTPException(HTTPException::TYPE_NICKNAME_ALREADY_EXISTS);
        }

        $users = $sdkForUsers->list(
            queries: [
                Query::equal('email', $email),
                Query::limit(1),
            ]
        );

        if ($users['total'] > 0) {
            throw new HTTPException(HTTPException::TYPE_EMAIL_ALREADY_EXISTS);
        }

        $token = 'sk_'.ID::unique(64);

        try {
            $user = $sdkForUsers->create(
                userId: ID::unique(),
                email: $email,
                password: $password,
                name: $nickname,
            );

            $profile = $sdkForTables->createRow(
                databaseId: 'main',
                tableId: 'profiles2',
                rowId: ID::unique(),
                data: [
                    'userId' => $user['$id'],
                    'nickname' => $nickname,
                    'token' => $token,
                ]
            );
        } catch (\Throwable $err) {
            try {
                if (isset($user)) {
                    $sdkForUsers->delete($user['$id']);
                }
            } catch (\Throwable $err) {
                // Just a cleanup
            }

            throw $err;
        }

        $response->json([
            'id' => $user['$id'],
            'email' => $user['email'],
            'nickname' => $profile['nickname'],
            'token' => $profile['token'],
        ]);

        // TODO: Tests, new DB each run
    }
}
