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

    public const TYPE_USER_ALREADY_EXISTS = 'user_already_exists';

    public const TYPE_WRONG_CREDENTIALS = 'wrong_credentials';

    public const TYPE_UNAUTHORIZED = 'unauthorized';

    public const TYPE_FORBIDDEN = 'forbidden';

    public const TYPE_USER_NOT_FOUND = 'user_not_found';

    public const TYPE_DUNGEON_NOT_FOUND = 'dungeon_not_found';

    public const TYPE_CURSOR_NOT_FOUND = 'cursor_not_found';

    public const TYPE_TILE_NOT_FOUND = 'tile_not_found';

    const EXCEPTIONS = [
        self::TYPE_TILE_NOT_FOUND => [
            'message' => 'Tile not found.',
            'code' => 404,
        ],
        self::TYPE_DUNGEON_NOT_FOUND => [
            'message' => 'Dungeon not found.',
            'code' => 404,
        ],
        self::TYPE_USER_NOT_FOUND => [
            'message' => 'User not found.',
            'code' => 404,
        ],
        self::TYPE_UNAUTHORIZED => [
            'message' => 'You are missing token in Authorization header.',
            'code' => 401,
        ],
        self::TYPE_FORBIDDEN => [
            'message' => 'Your token is not allowed to access this resource.',
            'code' => 403,
        ],
        self::TYPE_PASSWORDS_DO_NOT_MATCH => [
            'message' => 'Passwords do not match.',
            'code' => 400,
        ],
        self::TYPE_USER_ALREADY_EXISTS => [
            'message' => 'User with this email or nickname already exists.',
            'code' => 409,
        ],
        self::TYPE_WRONG_CREDENTIALS => [
            'message' => 'Wrong email or password.',
            'code' => 401,
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
        self::TYPE_CURSOR_NOT_FOUND => [
            'message' => 'Cursor not found.',
            'code' => 404,
        ],
    ];

    public function __construct(protected string $type, ?string $message = null)
    {
        $exception = self::EXCEPTIONS[$type] ?? [];
        $message = $message ?? $exception['message'] ?? 'Unknown error.';
        $code = $exception['code'] ?? 500;

        parent::__construct($message, $code);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
