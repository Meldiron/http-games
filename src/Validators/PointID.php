<?php

namespace HTTPGames\Validators;

use Utopia\Validator;

class PointID extends Validator
{
    protected string $message;

    /**
     * Get Description.
     *
     * Returns validator description
     */
    public function getDescription(): string
    {
        return $this->message;
    }

    /**
     * Expression constructor
     */
    public function __construct(
        protected readonly int $maxLength = 36,
        protected readonly int $minLength = 3,
    ) {
        $this->message = 'Parameter must contain at least '.$this->minLength.' chars, and at most '.$this->maxLength.' chars. Valid format is: x_y where x and y are integers';
    }

    /**
     * Is valid.
     *
     * Returns true if valid or false if not.
     */
    public function isValid($value): bool
    {
        if (! \is_string($value)) {
            return false;
        }

        if ($value === '') {
            return false;
        }

        if (\mb_strlen($value) > $this->maxLength) {
            return false;
        }

        if (\mb_strlen($value) < $this->minLength) {
            return false;
        }

        if (! (\str_contains($value, '_'))) {
            return false;
        }

        $parts = \explode('_', $value, 2);

        if (\count($parts) !== 2) {
            return false;
        }

        $x = $parts[0];
        $y = $parts[1];

        if (! (\ctype_digit($x))) {
            return false;
        }

        if (! (\ctype_digit($y))) {
            return false;
        }

        return true;
    }

    /**
     * Is array
     *
     * Function will return true if object is array.
     */
    public function isArray(): bool
    {
        return false;
    }

    /**
     * Get Type
     *
     * Returns validator type.
     */
    public function getType(): string
    {
        return self::TYPE_STRING;
    }
}
