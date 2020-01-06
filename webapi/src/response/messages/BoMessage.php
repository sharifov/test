<?php

namespace webapi\src\response\messages;

/**
 * Class BoCodeMessage
 */
class BoErrorMessage extends Message
{
    public function __construct()
    {
        parent::__construct(Message::BO_ERROR, true);
    }
}
