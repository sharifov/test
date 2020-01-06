<?php

namespace webapi\src\response;

/**
 * Class StatusMessage
 */
class StatusMessage extends Message
{
    public function __construct($value)
    {
        parent::__construct(Message::MESSAGE_STATUS, $value);
    }
}
