<?php

namespace frontend\widgets\multipleUpdate\cases;

/**
 * Class Message
 *
 * @property string $text
 */
abstract class Message
{
    protected $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    abstract public function format(): string;
}
