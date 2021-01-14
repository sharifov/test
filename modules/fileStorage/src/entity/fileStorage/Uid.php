<?php

namespace modules\fileStorage\src\entity\fileStorage;

/**
 * Class Uid
 *
 * @property string $value
 */
class Uid
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(md5(uniqid('', true)));
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
