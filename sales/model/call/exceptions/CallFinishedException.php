<?php

namespace sales\model\call\exceptions;

use Throwable;

/**
 * Class CallFinishedException
 *
 * @property $callSid
 */
class CallFinishedException extends \DomainException
{
    public $callSid;

    public function __construct($callSid, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Call is already finished.', $code, $previous);
        $this->callSid = $callSid;
    }
}
