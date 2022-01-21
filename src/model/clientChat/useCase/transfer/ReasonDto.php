<?php

namespace src\model\clientChat\useCase\transfer;

/**
 * Class ReasonDto
 * @package src\model\clientChat\useCase\transfer
 *
 * @property int $id
 * @property string $name
 * @property bool $requiredComment
 */
class ReasonDto
{
    public $id;
    public $name;
    public $requiredComment;

    public function __construct(int $id, string $name, bool $requiredComment)
    {
        $this->id = $id;
        $this->name = $name;
        $this->requiredComment = $requiredComment;
    }
}
