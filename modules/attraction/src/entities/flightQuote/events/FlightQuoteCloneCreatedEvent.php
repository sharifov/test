<?php

namespace modules\flight\src\entities\flightQuote\events;

use modules\flight\models\FlightQuote;

/**
 * Class FlightQuoteCloneCreatedEvent
 *
 * @property FlightQuote $quote
 */
class FlightQuoteCloneCreatedEvent
{
    public $quote;

    public function __construct(FlightQuote $quote)
    {
        $this->quote = $quote;
    }
}
