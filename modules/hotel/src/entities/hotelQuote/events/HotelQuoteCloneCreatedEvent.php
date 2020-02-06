<?php

namespace modules\hotel\src\entities\hotelQuote\events;

use modules\hotel\models\HotelQuote;

/**
 * Class HotelQuoteCloneCreatedEvent
 *
 * @property HotelQuote $hotelQuote
 */
class HotelQuoteCloneCreatedEvent
{
    public $hotelQuote;

    public function __construct(HotelQuote $hotelQuote)
    {
        $this->hotelQuote = $hotelQuote;
    }
}
