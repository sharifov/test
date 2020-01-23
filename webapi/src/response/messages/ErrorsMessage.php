<?php

namespace webapi\src\response\messages;

/**
 * Class ErrorsMessage
 */
class ErrorsMessage extends Message
{
    public function __construct($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        parent::__construct(Message::ERRORS_MESSAGE, ...[$value]);
    }
}
