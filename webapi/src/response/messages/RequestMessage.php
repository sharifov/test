<?php

namespace webapi\src\response\messages;

/**
 * Class RequestMessage
 */
class RequestMessage extends Message
{
    public function __construct(...$value)
    {
        parent::__construct(Message::REQUEST_MESSAGE, ...$value);
    }
}
