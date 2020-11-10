<?php

namespace sales\model\call\exceptions;

use Throwable;

class CallFinishedException extends \DomainException
{
    public function __construct($callSid, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Call is already finished. SID: ' . $callSid, $code, $previous);
    }
}
