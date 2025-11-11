<?php

namespace HTTPGames\Games\GridTrap\Dungeons;

use Appwrite\AppwriteException;
use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\UID;
use Utopia\Database\Document;
use Utopia\Platform\Action;
use Utopia\Response;
use Utopia\Validator\Range;
use Utopia\Validator\Text;
use Utopia\Validator\WhiteList;

class XList extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('GET')
            ->setHttpPath('/v1/games/grid-trap/dungeons')
            ->groups(['withSession'])
            ->param('limit', 25, new Range(1, 25), optional: true)
            ->param('cursor', '', new Text(255), optional: true)
            ->param('size', '', new Text(1024), optional: true)
            ->param('hardcore', '', new Text(1024), optional: true)
            ->inject('user')
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(int $limit, string $cursor, string $size, string $hardcore, Document $user, string $databaseId, TablesDB $sdkForTables, Response $response): void
    {
        /**
         * @var array<string> $sizeFilters
         */
        $sizeFilters = [];
        if (! empty($size)) {
            foreach (\explode(',', $size) as $size) {
                if (! ((new WhiteList(['4x4', '7x7', '10x10']))->isValid($size))) {
                    throw new HTTPException(HTTPException::TYPE_BAD_REQUEST, 'Invalid size: Must be one of "small", "medium", or "large". For more values, use comma-separated list');
                }
                $sizes[] = $size;
            }
        }

        /**
         * @var array<string> $hardcoreFilters
         */
        $hardcoreFilters = [];
        if (! empty($hardcore)) {
            foreach (\explode(',', $hardcore) as $hardcore) {
                if (! ((new WhiteList(['true', 'false']))->isValid($hardcore))) {
                    throw new HTTPException(HTTPException::TYPE_BAD_REQUEST, 'Invalid hardcore: Must be one of "true" or "false". For more values, use comma-separated list');
                }
                $hardcoreFilters[] = $hardcore;
            }
        }

        if (! empty($cursor)) {
            $cursorDirection = \explode(':', $cursor, 2)[0];
            $cursorId = \explode(':', $cursor, 2)[1];

            if (
                ! ((new UID)->isValid($cursorId)) ||
                ! ((new WhiteList(['before', 'after']))->isValid($cursorDirection))
            ) {
                throw new HTTPException(HTTPException::TYPE_BAD_REQUEST, 'Invalid cursor: Must be in format "before:id" or "after:id"');
            }
        }

        $queries = [
            Query::equal('userId', $user->getId()),
            Query::limit($limit),
        ];

        if (\count($sizeFilters) > 0) {
            $queries[] = Query::equal('size', $sizeFilters);
        }

        if (\count($hardcoreFilters) > 0) {
            $queries[] = Query::equal('hardcore', $hardcoreFilters);
        }

        if (! empty($cursorId) && ! empty($cursorDirection)) {
            if ($cursorDirection === 'before') {
                $queries[] = Query::cursorBefore($cursorId);
            } elseif ($cursorDirection === 'after') {
                $queries[] = Query::cursorAfter($cursorId);
            }
        }

        try {
            $dungeons = $sdkForTables->listRows($databaseId, 'gridTrapDungeons', $queries);
        } catch (AppwriteException $err) {
            if ($err->getType() === 'general_cursor_not_found') {
                throw new HTTPException(HTTPException::TYPE_CURSOR_NOT_FOUND);
            }

            throw $err;
        }

        $cursorNext = \end($dungeons['rows'])['$id'] ?? null;
        $cursorPrevious = \reset($dungeons['rows'])['$id'] ?? null;

        $response->json([
            'total' => $dungeons['total'],
            'cursorNext' => $cursorNext ? 'after:'.$cursorNext : null,
            'cursorPrevious' => $cursorPrevious ? 'before:'.$cursorPrevious : null,
            'dungeons' => \array_map(function (array $dungeon) {
                return [
                    'id' => $dungeon['$id'],
                    'size' => $dungeon['size'],
                    'hardcore' => $dungeon['hardcore'],
                    'seed' => $dungeon['seed'],
                ];
            }, $dungeons['rows']),
        ]);
    }
}
