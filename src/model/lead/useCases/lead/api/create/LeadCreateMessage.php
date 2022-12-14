<?php

namespace src\model\lead\useCases\lead\api\create;

use webapi\src\response\messages\Message;

class LeadCreateMessage extends Message
{
    public const KEY = 'lead';

    public function __construct(...$value)
    {
        parent::__construct(self::KEY, ...$value);
    }
}
