<?php

namespace src\model\clientChat\useCase\close;

/**
 * Class ReasonDto
 * @package src\model\clientChat\useCase\close
 *
 * @property int $id
 * @property string $name
 * @property bool $commentRequired
 */
class ReasonDto
{
    public $id;
    public $name;
    public $commentRequired;

    public function __construct(int $id, string $name, bool $commentRequired)
    {
        $this->id = $id;
        $this->name = $name;
        $this->commentRequired = $commentRequired;
    }

    public function isCommentRequired(): bool
    {
        return $this->commentRequired;
    }
}
