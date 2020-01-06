<?php

namespace webapi\src\response\messages;

/**
 * Class SourceMessage
 */
class SourceMessage extends Message
{
    public function __construct($value)
    {
        parent::__construct(Message::SOURCE_MESSAGE, $value);
    }
}
