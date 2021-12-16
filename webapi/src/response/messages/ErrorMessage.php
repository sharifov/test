<?php

namespace webapi\src\response\messages;

class ErrorMessage extends Message
{
    public function __construct($value = null)
    {
        parent::__construct(Message::ERROR_MESSAGE, $value);
    }
}
