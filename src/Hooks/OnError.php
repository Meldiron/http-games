<?php

namespace HTTPGames\Hooks;

use HTTPGames\Exceptions\HTTPException;
use Utopia\Exception as UtopiaException;
use Utopia\Platform\Action;
use Utopia\Response;

class OnError extends Action
{
    public function __construct()
    {
        $this
            ->setType(Action::TYPE_ERROR)
            ->groups(['*'])
            ->inject('error')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(\Throwable $error, Response $response): void
    {
        if (($_ENV['_APP_LOGGING'] ?? 'disabled') === 'enabled') {
            echo '----'.PHP_EOL;
            echo 'Type: '.\get_class($error).PHP_EOL;
            echo 'Message: '.$error->getMessage().PHP_EOL;
            echo 'Trace: '.$error->getTraceAsString().PHP_EOL;
            echo 'File: '.$error->getFile().':'.$error->getLine().PHP_EOL;
            echo '----'.PHP_EOL;
        }

        $publicError = new HTTPException(HTTPException::TYPE_INTERNAL_SERVER_ERROR);

        if ($error instanceof HTTPException) {
            $publicError = $error;
        }

        if ($error instanceof UtopiaException) {
            if ($error->getCode() === 404) {
                $publicError = new HTTPException(HTTPException::TYPE_PATH_NOT_FOUND);
            } else {
                $publicError = $error;
                $type = HTTPException::TYPE_BAD_REQUEST;
            }
        }

        $code = $publicError->getCode();
        if ($code === 0) {
            $code = 500;
        }

        if (empty($type)) {
            if (\method_exists($publicError, 'getType')) {
                $type = $publicError->getType();
            } else {
                $type = HTTPException::TYPE_INTERNAL_SERVER_ERROR;
            }
        }

        $response->setStatusCode($code);
        $response->json([
            'type' => $type,
            'message' => $publicError->getMessage(),
        ]);
    }
}
