<?php

namespace HTTPGames\Utility;

// Seeded randomness, useful utility for many games
class Randomness
{
    private int $seed;

    private int $iterator;

    public function __construct(int $seed)
    {
        $this->seed = $seed;
        $this->iterator = 0;
    }

    protected function generate(): float
    {
        // Combine the integer seed and the iterator to create a unique seed for mt_srand
        // Use multiplication by a large prime to avoid collisions (e.g. seed=10,iter=1 vs seed=11,iter=0)
        $combinedSeed = $this->seed * 1000003 + $this->iterator;
        mt_srand($combinedSeed);

        // Generate a random integer between 0 and mt_getrandmax()
        $randomNumber = mt_rand();

        // Scale the random number to be between 0 and 1
        $scaledNumber = $randomNumber / mt_getrandmax();

        // Increment the iterator for the next generation
        $this->iterator++;

        return $scaledNumber;
    }

    /**
     * Both min and max are inclusive.
     */
    public function generateInRange(int $min, int $max): int
    {
        // Generate a float between 0 and 1
        $randomFloat = $this->generate();

        // Scale to the desired range (inclusive of both min and max)
        $range = $max - $min + 1;
        $randomInt = $min + (int) floor($randomFloat * $range);

        return $randomInt;
    }
}
