<?php

namespace webapi\src\response;

/**
 * Class StatusCodeMessage
 */
class StatusCodeMessage extends Message
{
    public function __construct($value)
    {
        parent::__construct(Message::MESSAGE_STATUS_CODE, $value);
    }
}
