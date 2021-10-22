<?php

namespace webapi\src\response\messages;

class TypeMessage extends Message
{
    public function __construct($value = null)
    {
        parent::__construct(Message::TYPE_MESSAGE, $value);
    }
}
