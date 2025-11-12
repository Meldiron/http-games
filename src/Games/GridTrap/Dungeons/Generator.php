<?php

namespace HTTPGames\Games\GridTrap\Dungeons;

// TODO: This should be unit-tested

class Generator
{
    public function __construct(protected int $minX, protected int $maxX, protected int $minY, protected int $maxY) {}

    /**
     * @return array<int, array{int, int}>
     */
    public function generatePath(int $startX, int $startY, int $endX, int $endY): array
    {
        // Check if start and end positions are within bounds
        if ($startX < $this->minX || $startX > $this->maxX ||
            $startY < $this->minY || $startY > $this->maxY ||
            $endX < $this->minX || $endX > $this->maxX ||
            $endY < $this->minY || $endY > $this->maxY) {
            return [];
        }

        // If start equals end, return just the start position
        if ($startX === $endX && $startY === $endY) {
            return [[$startX, $startY]];
        }

        // Initialize cell states: 0 = Open, 1 = Blocked, 2 = Forced
        $cellStates = [];
        $openCells = [];

        // Initialize all cells as open
        for ($x = $this->minX; $x <= $this->maxX; $x++) {
            for ($y = $this->minY; $y <= $this->maxY; $y++) {
                $key = "$x,$y";
                $cellStates[$key] = 0; // Open
                $openCells[] = [$x, $y];
            }
        }

        // Start and end are forced
        $startKey = "$startX,$startY";
        $endKey = "$endX,$endY";
        $cellStates[$startKey] = 2; // Forced
        $cellStates[$endKey] = 2; // Forced

        // Remove start and end from open cells
        $openCells = array_filter($openCells, function ($cell) use ($startX, $startY, $endX, $endY) {
            return ! ($cell[0] === $startX && $cell[1] === $startY) &&
                   ! ($cell[0] === $endX && $cell[1] === $endY);
        });
        $openCells = array_values($openCells);

        // Find initial witness path
        $witness = $this->findShortestPathWithObstacles($startX, $startY, $endX, $endY, $cellStates);

        if (empty($witness)) {
            return []; // No initial path possible
        }

        // Main chiseling loop
        while (! empty($openCells)) {
            // Pick a random open cell
            $randomIndex = array_rand($openCells);
            $randomCell = $openCells[$randomIndex];
            $cellKey = "{$randomCell[0]},{$randomCell[1]}";

            // Remove from open cells
            array_splice($openCells, $randomIndex, 1);

            // Set cell to blocked
            $cellStates[$cellKey] = 1; // Blocked

            // Check if this cell is in the witness path
            $isInWitness = false;
            foreach ($witness as $witnessCell) {
                if ($witnessCell[0] === $randomCell[0] && $witnessCell[1] === $randomCell[1]) {
                    $isInWitness = true;
                    break;
                }
            }

            if ($isInWitness) {
                // Try to find a new path
                $newPath = $this->findShortestPathWithObstacles($startX, $startY, $endX, $endY, $cellStates);

                if (empty($newPath)) {
                    // No path possible, this cell is forced (required waypoint)
                    $cellStates[$cellKey] = 2; // Forced
                } else {
                    // New path found, update witness
                    $witness = $newPath;
                }
            }
            // If not in witness, we can safely keep it blocked
        }

        // Return the final witness path
        return $witness;
    }

    private function heuristic(int $x1, int $y1, int $x2, int $y2): float
    {
        // Manhattan distance
        return abs($x1 - $x2) + abs($y1 - $y2);
    }

    /**
     * @param  array<int, array{int, int}>  $openSet
     * @param  array<string, float>  $fScore
     * @return array{int, int}
     */
    private function getLowestFScore(array $openSet, array $fScore): array
    {
        $lowest = $openSet[0]; // Initialize with first element
        $lowestScore = PHP_FLOAT_MAX;

        foreach ($openSet as $node) {
            $key = "{$node[0]},{$node[1]}";
            $score = $fScore[$key] ?? PHP_FLOAT_MAX;
            if ($score < $lowestScore) {
                $lowestScore = $score;
                $lowest = $node;
            }
        }

        return $lowest;
    }

    /**
     * @param  array<string, array{int, int}>  $cameFrom
     * @param  array{int, int}  $current
     * @return array<int, array{int, int}>
     */
    private function reconstructPath(array $cameFrom, array $current): array
    {
        $path = [$current];
        $currentKey = "{$current[0]},{$current[1]}";

        while (isset($cameFrom[$currentKey])) {
            $current = $cameFrom[$currentKey];
            $currentKey = "{$current[0]},{$current[1]}";
            array_unshift($path, $current);
        }

        return $path;
    }

    /**
     * @param  array<string, int>  $cellStates
     * @return array<int, array{int, int}>
     */
    private function findShortestPathWithObstacles(int $startX, int $startY, int $endX, int $endY, array $cellStates): array
    {
        // If start equals end, return just the start position
        if ($startX === $endX && $startY === $endY) {
            return [[$startX, $startY]];
        }

        // A* algorithm implementation with obstacle avoidance
        $openSet = [[$startX, $startY]];
        $cameFrom = [];
        $gScore = [];
        $fScore = [];

        // Initialize scores
        $startKey = "$startX,$startY";
        $gScore[$startKey] = 0;
        $fScore[$startKey] = $this->heuristic($startX, $startY, $endX, $endY);

        while (! empty($openSet)) {
            // Find node in openSet with lowest fScore
            $current = $this->getLowestFScore($openSet, $fScore);
            $currentKey = "{$current[0]},{$current[1]}";

            // Remove current from openSet
            $openSet = array_filter($openSet, function ($node) use ($current) {
                return ! ($node[0] === $current[0] && $node[1] === $current[1]);
            });
            $openSet = array_values($openSet);

            // Check if we reached the goal
            if ($current[0] === $endX && $current[1] === $endY) {
                return $this->reconstructPath($cameFrom, $current);
            }

            // Check all neighbors
            $neighbors = $this->getValidNeighbors($current[0], $current[1], $cellStates);
            foreach ($neighbors as $neighbor) {
                $neighborKey = "{$neighbor[0]},{$neighbor[1]}";
                $tentativeGScore = $gScore[$currentKey] + 1; // Distance between neighbors is 1

                if (! isset($gScore[$neighborKey]) || $tentativeGScore < $gScore[$neighborKey]) {
                    $cameFrom[$neighborKey] = $current;
                    $gScore[$neighborKey] = $tentativeGScore;
                    $fScore[$neighborKey] = $gScore[$neighborKey] + $this->heuristic($neighbor[0], $neighbor[1], $endX, $endY);

                    // Add to openSet if not already there
                    $inOpenSet = false;
                    foreach ($openSet as $node) {
                        if ($node[0] === $neighbor[0] && $node[1] === $neighbor[1]) {
                            $inOpenSet = true;
                            break;
                        }
                    }
                    if (! $inOpenSet) {
                        $openSet[] = $neighbor;
                    }
                }
            }
        }

        // No path found
        return [];
    }

    /**
     * @param  array<string, int>  $cellStates
     * @return array<int, array{int, int}>
     */
    private function getValidNeighbors(int $x, int $y, array $cellStates): array
    {
        $neighbors = [];
        $directions = [
            [0, 1],  // up
            [0, -1], // down
            [1, 0],  // right
            [-1, 0],  // left
        ];

        foreach ($directions as $dir) {
            $newX = $x + $dir[0];
            $newY = $y + $dir[1];

            // Check if neighbor is within bounds
            if ($newX >= $this->minX && $newX <= $this->maxX &&
                $newY >= $this->minY && $newY <= $this->maxY) {

                $key = "$newX,$newY";
                $state = $cellStates[$key] ?? 0;

                // Only allow movement through open (0) or forced (2) cells, not blocked (1) cells
                if ($state !== 1) {
                    $neighbors[] = [$newX, $newY];
                }
            }
        }

        return $neighbors;
    }
}
