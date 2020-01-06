<?php

namespace webapi\src\response\messages;

/**
 * Class TechnicalMessage
 */
class TechnicalMessage extends Message
{
    public function __construct(...$value)
    {
        parent::__construct(Message::TECHNICAL_MESSAGE, ...$value);
    }
}
