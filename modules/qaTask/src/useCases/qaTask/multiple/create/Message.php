<?php

namespace modules\qaTask\src\useCases\qaTask\multiple\create;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;

/**
 * Class Message
 *
 * @property string $text
 */
abstract class Message
{
    protected $text;

    public function __construct(int $type, int $id, string $message)
    {
        $this->text = QaTaskObjectType::getName($type) . ' Id: ' . $id . ' ' . $message;
    }

    abstract public function format(): string;
}
