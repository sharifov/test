<?php

namespace webapi\src\response\messages;

/**
 * Class StatusCodeMessage
 */
class StatusCodeMessage extends Message
{
    public function __construct($value = null)
    {
        parent::__construct(Message::STATUS_CODE_MESSAGE, $value);
    }
}
