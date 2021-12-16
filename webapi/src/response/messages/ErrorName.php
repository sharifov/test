<?php

namespace webapi\src\response\messages;

class ErrorName extends Message
{
    public function __construct($value = null)
    {
        parent::__construct(Message::NAME_MESSAGE, $value);
    }
}
