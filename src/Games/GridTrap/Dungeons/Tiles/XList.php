<?php

namespace HTTPGames\Games\GridTrap\Dungeons\Tiles;

use Appwrite\AppwriteException;
use Appwrite\Query;
use Appwrite\Services\TablesDB;
use HTTPGames\Exceptions\HTTPException;
use HTTPGames\Validators\UID;
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
            ->setHttpPath('/v1/games/grid-trap/dungeons/:dungeonId/tiles')
            ->groups(['withSession', 'withGridTrapDungeon'])
            ->param('dungeonId', '', new UID)
            ->param('limit', 25, new Range(1, 25), optional: true)
            ->param('cursor', '', new Text(255), optional: true)
            ->param('x', '', new Text(1024), optional: true)
            ->param('y', '', new Text(1024), optional: true)
            ->param('type', '', new Text(1024), optional: true)
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(
        string $dungeonId,
        int $limit,
        string $cursor,
        string $x,
        string $y,
        string $type,
        string $databaseId,
        TablesDB $sdkForTables,
        Response $response
    ): void {
        /**
         * @var array<int> $xFilters
         */
        $xFilters = [];
        if (! empty($x)) {
            foreach (\explode(',', $x) as $x) {
                if (! ((new Range(0, 1024))->isValid($x))) {
                    throw new HTTPException(HTTPException::TYPE_BAD_REQUEST, 'Invalid x: Must be positive integer less than 1024. For more values, use comma-separated list');
                }
                $xFilters[] = (int) $x;
            }
        }

        /**
         * @var array<int> $yFilters
         */
        $yFilters = [];
        if (! empty($y)) {
            foreach (\explode(',', $y) as $y) {
                if (! ((new Range(0, 1024))->isValid($y))) {
                    throw new HTTPException(HTTPException::TYPE_BAD_REQUEST, 'Invalid y: Must be positive integer less than 1024. For more values, use comma-separated list');
                }
                $yFilters[] = (int) $y;
            }
        }

        /**
         * @var array<string> $typeFilters
         */
        $typeFilters = [];
        if (! empty($type)) {
            foreach (\explode(',', $type) as $type) {
                if (! ((new WhiteList(['wall', 'ground', 'entrance', 'rope']))->isValid($type))) {
                    throw new HTTPException(HTTPException::TYPE_BAD_REQUEST, 'Invalid type: Must be one of "wall" or "ground" or "entrance" or "rope". For more values, use comma-separated list');
                }
                $typeFilters[] = $type;

                if ($type === 'ground') {
                    $typeFilters[] = 'trap';
                }
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
            Query::equal('dungeonId', $dungeonId),
            Query::limit($limit),
        ];

        if (\count($xFilters) > 0) {
            $queries[] = Query::equal('x', $xFilters);
        }

        if (\count($yFilters) > 0) {
            $queries[] = Query::equal('y', $yFilters);
        }

        if (\count($typeFilters) > 0) {
            $queries[] = Query::equal('type', $typeFilters);
        }

        if (! empty($cursorId) && ! empty($cursorDirection)) {
            if ($cursorDirection === 'before') {
                $queries[] = Query::cursorBefore($cursorId);
            } elseif ($cursorDirection === 'after') {
                $queries[] = Query::cursorAfter($cursorId);
            }
        }

        try {
            $tiles = $sdkForTables->listRows($databaseId, 'gridTrapTiles', $queries);
        } catch (AppwriteException $err) {
            if ($err->getType() === 'general_cursor_not_found') {
                throw new HTTPException(HTTPException::TYPE_CURSOR_NOT_FOUND);
            }

            throw $err;
        }

        $cursorNext = \end($tiles['rows'])['$id'] ?? null;
        $cursorPrevious = \reset($tiles['rows'])['$id'] ?? null;

        $response->json([
            'total' => $tiles['total'],
            'cursorNext' => $cursorNext ? 'after:'.$cursorNext : null,
            'cursorPrevious' => $cursorPrevious ? 'before:'.$cursorPrevious : null,
            'tiles' => \array_map(function (array $tile) {
                $type = $tile['type'];

                if ($type == 'trap') {
                    $type = 'ground';
                }

                return [
                    'x' => $tile['position'][0],
                    'y' => $tile['position'][1],
                    'type' => $type,
                ];
            }, $tiles['rows']),
        ]);
    }
}
