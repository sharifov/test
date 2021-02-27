<?php

namespace modules\order\src\processManager\events;

/**
 * Class FlightQuoteBookedEvent
 *
 * @property $quoteId
 */
class FlightQuoteBookedEvent
{
    public int $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }
}
