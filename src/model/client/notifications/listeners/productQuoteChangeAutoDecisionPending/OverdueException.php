<?php

namespace src\model\client\notifications\listeners\productQuoteChangeAutoDecisionPending;

use Throwable;

class OverdueException extends \DomainException
{
    public function __construct(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, $code = 0, Throwable $previous = null)
    {
        parent::__construct('"End"(' . $endDate->format('Y-m-d H:i:s') . ') time must less then "start"(' . $startDate->format('Y-m-d H:i:s') . ') time.', $code, $previous);
    }
}
