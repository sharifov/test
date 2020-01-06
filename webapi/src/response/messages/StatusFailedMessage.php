<?php

namespace webapi\src\response\messages;

/**
 * Class StatusFailedMessage
 */
class StatusFailedMessage extends Message
{
    private const DEFAULT_MESSAGE = 'Failed';

    public function __construct()
    {
        parent::__construct(Message::STATUS_MESSAGE, self::DEFAULT_MESSAGE);
    }
}
