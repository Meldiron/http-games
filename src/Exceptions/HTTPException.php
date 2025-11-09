<?php

namespace HTTPGames\Exceptions;

class HTTPException extends \Exception
{
    public const TYPE_PASSWORDS_DO_NOT_MATCH = 'passwords_do_not_match';

    public const TYPE_NICKNAME_ALREADY_EXISTS = 'nickname_already_exists';

    public const TYPE_UNKNOWN = 'unknown';

    public const TYPE_INTERNAL_SERVER_ERROR = 'internal_server_error';

    public const TYPE_PATH_NOT_FOUND = 'path_not_found';

    public const TYPE_BAD_REQUEST = 'bad_request';

    public const TYPE_EMAIL_ALREADY_EXISTS = 'email_already_exists';

    const EXCEPTIONS = [
        self::TYPE_PASSWORDS_DO_NOT_MATCH => [
            'message' => 'Passwords do not match.',
            'code' => 400,
        ],
        self::TYPE_NICKNAME_ALREADY_EXISTS => [
            'message' => 'Nickname already exists.',
            'code' => 409,
        ],
        self::TYPE_UNKNOWN => [
            'message' => 'Unknown error.',
            'code' => 500,
        ],
        self::TYPE_INTERNAL_SERVER_ERROR => [
            'message' => 'Internal Server Error',
            'code' => 500,
        ],
        self::TYPE_PATH_NOT_FOUND => [
            'message' => 'Path not found.',
            'code' => 404,
        ],
        self::TYPE_BAD_REQUEST => [
            'message' => 'Your request body is not valid.',
            'code' => 400,
        ],
        self::TYPE_EMAIL_ALREADY_EXISTS => [
            'message' => 'User with this email already exists.',
            'code' => 409,
        ],
    ];

    public function __construct(protected string $type)
    {
        $exception = self::EXCEPTIONS[$type] ?? [];
        $message = $exception['message'] ?? 'Unknown error.';
        $code = $exception['code'] ?? 500;

        parent::__construct($message, $code);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
