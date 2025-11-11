<?php

namespace HTTPGames\Games\GridTrap\Dungeons;

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
            ->inject('user')
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(int $limit, string $cursor, Document $user, string $databaseId, TablesDB $sdkForTables, Response $response): void
    {
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

        if (! empty($cursorId) && ! empty($cursorDirection)) {
            if ($cursorDirection === 'before') {
                $queries[] = Query::cursorBefore($cursorId);
            } elseif ($cursorDirection === 'after') {
                $queries[] = Query::cursorAfter($cursorId);
            }
        }

        $dungeons = $sdkForTables->listRows($databaseId, 'gridTrapDungeons', $queries);

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
