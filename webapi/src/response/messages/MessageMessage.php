<?php

namespace webapi\src\response\messages;

/**
 * Class MessageMessage
 */
class MessageMessage extends Message
{
    public function __construct($value = null)
    {
        parent::__construct(Message::MESSAGE_MESSAGE, $value);
    }
}
