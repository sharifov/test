<?php

namespace webapi\src\response\messages;

class DetailErrorMessage extends Message
{
    public function __construct(...$values)
    {
        parent::__construct(Message::DETAIL_ERROR_MESSAGE, ...$values);
    }
}
