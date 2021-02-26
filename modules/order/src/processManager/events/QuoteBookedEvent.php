<?php

namespace modules\order\src\processManager\events;

/**
 * Class QuoteBookedEvent
 *
 * @property $quoteId
 */
class QuoteBookedEvent
{
    public int $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }
}
