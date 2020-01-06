<?php

namespace webapi\src\response\messages;

/**
 * Class BoMessage
 */
class BoMessage extends Message
{
    public function __construct()
    {
        parent::__construct(Message::SOURCE_MESSAGE, Sources::BO);
    }
}
