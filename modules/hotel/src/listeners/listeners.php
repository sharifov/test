<?php

use modules\hotel\src\entities\hotel\events\HotelUpdateRequestEvent;
use modules\hotel\src\entities\hotelQuote\events\HotelQuoteCloneCreatedEvent;
use modules\hotel\src\entities\hotelQuoteRoom\events\HotelQuoteRoomCloneCreatedEvent;

return [
    HotelQuoteCloneCreatedEvent::class => [],
    HotelQuoteRoomCloneCreatedEvent::class => [],
    HotelUpdateRequestEvent::class => [],
];
