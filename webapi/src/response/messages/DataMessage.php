<?php

namespace webapi\src\response\messages;

/**
 * Class DataMessage
 */
class DataMessage extends Message
{
    public function __construct(...$value)
    {
        parent::__construct(Message::DATA_MESSAGE, ...$value);
    }
}
