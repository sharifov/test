<?php

namespace modules\hotel\src\entities\hotelQuote\events;

use modules\hotel\models\HotelQuote;

/**
 * Class HotelQuoteCloneCreatedEvent
 *
 * @property HotelQuote $quote
 */
class HotelQuoteCloneCreatedEvent
{
    public $quote;

    public function __construct(HotelQuote $quote)
    {
        $this->quote = $quote;
    }
}
