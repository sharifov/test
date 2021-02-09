<?php

namespace modules\fileStorage\src\entity\fileStorage;

/**
 * Class Path
 *
 * @property string $value
 */
class Path
{
    private string $value;

    public function __construct(string $value)
    {
        if (strlen($value) > 250) {
            throw new \DomainException('Path must be less than 250.');
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
