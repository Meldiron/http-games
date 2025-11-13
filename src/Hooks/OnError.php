<?php

namespace HTTPGames\Hooks;

use Appwrite\AppwriteException;
use HTTPGames\Exceptions\HTTPException;
use Utopia\Exception as UtopiaException;
use Utopia\Platform\Action;
use Utopia\Request;
use Utopia\Response;

class OnError extends Action
{
    public function __construct()
    {
        $this->setType(Action::TYPE_ERROR)
            ->groups(['*'])
            ->inject('error')
            ->inject('request')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(\Throwable $error, Request $request, Response $response): void
    {
        \error_log('----');
        \error_log('HTTP Request Details:');
        \error_log('Method: '.$request->getMethod());
        \error_log('URI: '.$request->getURI());
        \error_log('Error Details:');
        \error_log('Type: '.\get_class($error));
        \error_log('Message: '.$error->getMessage());
        \error_log('Trace: '.$error->getTraceAsString());
        \error_log('File: '.$error->getFile().':'.$error->getLine());
        if ($error instanceof AppwriteException) {
            \error_log('Appwrite Type: '.$error->getType());
        }
        \error_log('----');

        $publicError = new HTTPException(
            HTTPException::TYPE_INTERNAL_SERVER_ERROR,
        );

        if ($error instanceof HTTPException) {
            $publicError = $error;
        }

        if ($error instanceof UtopiaException) {
            if ($error->getCode() === 404) {
                $publicError = new HTTPException(
                    HTTPException::TYPE_PATH_NOT_FOUND,
                );
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
