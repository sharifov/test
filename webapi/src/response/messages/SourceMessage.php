<?php

namespace webapi\src\response\messages;

/**
 * Class SourceMessage
 */
class SourceMessage extends Message
{
    public function __construct($type, $statusCode)
    {
        parent::__construct(Message::SOURCE_MESSAGE, [
            'type' => (int)$type,
            'status' => (int)$statusCode,
        ]);
    }
}
