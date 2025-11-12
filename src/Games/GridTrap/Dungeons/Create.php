<?php

namespace HTTPGames\Games\GridTrap\Dungeons;

use Appwrite\ID;
use Appwrite\Services\TablesDB;
use HTTPGames\Utility\Randomness;
use Utopia\Database\Document;
use Utopia\Platform\Action;
use Utopia\Response;
use Utopia\Validator\Boolean;
use Utopia\Validator\Range;
use Utopia\Validator\WhiteList;

class Create extends Action
{
    public function __construct()
    {
        $this
            ->setHttpMethod('POST')
            ->setHttpPath('/v1/games/grid-trap/dungeons')
            ->groups(['withSession'])
            ->param('size', '4x4', new WhiteList(['4x4', '7x7', '10x10']), optional: true)
            ->param('hardcore', false, new Boolean, optional: true)
            ->param('seed', 0, new Range(1, 9223372036), optional: true)
            ->inject('user')
            ->inject('databaseId')
            ->inject('sdkForTables')
            ->inject('response')
            ->callback($this->action(...));
    }

    public function action(string $size, bool $hardcore, int $seed, Document $user, string $databaseId, TablesDB $sdkForTables, Response $response): void
    {
        /*
        * 3x3 example of a map
        * e=enterance
        * r=rope
        * .=wall
        * #=trap/ground
        *
        *  ...
        * ..r..
        * .###.
        * .###.
        * .###.
        * .e...
        * ...
        */

        $seedCustomized = true;
        if ($seed === 0) {
            $seedCustomized = false;
            $seed = \rand(0, 9223372036);
        }

        $width = (int) \explode('x', $size, 2)[0];
        $height = (int) \explode('x', $size, 2)[1];

        $randomness = new Randomness($seed);

        $tiles = [];

        $startX = $randomness->generateInRange(1, $width);
        $startY = 1;
        $tiles[] = new Document([
            'x' => $startX,
            'y' => $startY,
            'type' => 'enterance',
        ]);

        $endX = $randomness->generateInRange(1, $width);
        $endY = $height + 2;
        $tiles[] = new Document([
            'x' => $endX,
            'y' => $endY,
            'type' => 'rope',
        ]);

        // Rectnagle grid of ground tiles
        // if size is 3, then from [1,2] to [3,4]
        for ($x = 1; $x <= $width; $x++) {
            for ($y = 2; $y <= $height + 1; $y++) {
                $tiles[] = new Document([
                    'x' => $x,
                    'y' => $y,
                    'type' => 'trap', // Later we change some to "ground"
                ]);
            }
        }

        // Generate path and mark some trap tiles as ground
        $generator = new Generator(
            minX: 1,
            maxX: $width,
            minY: 2,
            maxY: $height + 1
        );
        $path = $generator->generatePath(
            startX: $startX,
            startY: $startY + 1,
            endX: $endX,
            endY: $endY - 1
        );

        foreach ($path as $point) {
            foreach ($tiles as $tile) {
                if ($tile->getAttribute('x') === $point[0] && $tile->getAttribute('y') === $point[1]) {
                    $tile->setAttribute('type', 'ground');
                }
            }
        }

        // Walls around every tile, if it's position is currently empty
        foreach ($tiles as $tile) {
            // All 8 positions around
            $tileX = $tile->getAttribute('x');
            $tileY = $tile->getAttribute('y');

            for ($x = $tileX - 1; $x <= $tileX + 1; $x++) {
                for ($y = $tileY - 1; $y <= $tileY + 1; $y++) {
                    if ($x === $tileX && $y === $tileY) {
                        continue;
                    }

                    $tileAlreadyExists = false;
                    foreach ($tiles as $tileSearched) {
                        if ($tileSearched->getAttribute('x') === $x && $tileSearched->getAttribute('y') === $y) {
                            $tileAlreadyExists = true;
                            break;
                        }
                    }

                    if ($tileAlreadyExists) {
                        continue;
                    }

                    $tiles[] = new Document([
                        'x' => $x,
                        'y' => $y,
                        'type' => 'wall',
                    ]);
                }
            }
        }

        $dungeon = $sdkForTables->createRow($databaseId, 'gridTrapDungeons', ID::unique(), [
            'userId' => $user->getId(),
            'size' => $size,
            'hardcore' => $hardcore,
            'cartographerTrapped' => false,
            'cartographerPosition' => [$startX, $startY],
            'seed' => $seed,
            'seedCustomized' => $seedCustomized,
            'tiles' => \array_map(function (Document $tile) {
                return [
                    'position' => [$tile->getAttribute('x'), $tile->getAttribute('y')],
                    'type' => $tile->getAttribute('type'),
                ];
            }, $tiles),
        ]);

        $output = [
            'id' => $dungeon['$id'],
            'size' => $dungeon['size'],
            'hardcore' => $dungeon['hardcore'],
            'seed' => $dungeon['seed'],
        ];

        // \var_dump($this->visualizeDungeon($tiles));

        $response->setStatusCode(Response::STATUS_CODE_CREATED);
        $response->json($output);
    }

    /**
     * Visualize the dungeon using emojis.
     * TODO: Remove this once not needed anymore
     *
     * @param  array<Document>  $tiles  The tiles of the dungeon.
     */
    protected function visualizeDungeon(array $tiles): string
    {
        $emojis = [
            'wall' => '🟫',
            'enterance' => '🍙',
            'rope' => '🧗',
            'ground' => '🆗',
            'trap' => '🕳️',
        ];

        // Create a grid map for quick lookup
        $grid = [];
        $maxX = 0;
        $maxY = 0;
        $minX = 0;
        $minY = 0;
        foreach ($tiles as $tile) {
            $x = $tile->getAttribute('x');
            $y = $tile->getAttribute('y');
            $type = $tile->getAttribute('type');
            $grid[$y][$x] = $type;

            if ($x > $maxX) {
                $maxX = $x;
            }
            if ($y > $maxY) {
                $maxY = $y;
            }
            if ($x < $minX) {
                $minX = $x;
            }
            if ($y < $minY) {
                $minY = $y;
            }
        }

        // Generate verbose string
        $verbose = '';
        for ($y = $maxY; $y >= $minY; $y--) { // Higher Y means higher row, so start from max
            for ($x = $minX; $x <= $maxX; $x++) { // Higher X means later in line
                if (isset($grid[$y][$x])) {
                    $verbose .= $emojis[$grid[$y][$x]];
                } else {
                    $verbose .= '⬛'; // Empty space
                }
            }
            $verbose .= "\n";
        }

        return "\n".$verbose."\n";
    }
}
