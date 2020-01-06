<?php

namespace webapi\src\response\messages;

/**
 * Class CodeMessage
 */
class CodeMessage extends Message
{
    public function __construct($value = null)
    {
        parent::__construct(Message::CODE_MESSAGE, $value);
    }
}
