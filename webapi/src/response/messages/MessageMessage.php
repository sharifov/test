<?php

namespace webapi\src\response;

/**
 * Class MessageMessage
 */
class MessageMessage extends Message
{
    public function __construct($value)
    {
        parent::__construct(Message::MESSAGE_MESSAGE, $value);
    }
}
