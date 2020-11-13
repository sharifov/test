<?php

namespace sales\model\call\exceptions;

use Throwable;

class UniqueCallNotFoundException extends \DomainException
{
    public function __construct($callSid, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Detected Duplicate Call. But not found Call. SID: ' . $callSid, $code, $previous);
    }
}
