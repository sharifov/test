<?php

namespace modules\hotel\src\entities\hotelQuoteRoom\events;

use modules\hotel\models\HotelQuoteRoom;

/**
 * Class HotelQuoteRoomCloneCreatedEvent
 *
 * @property HotelQuoteRoom $room
 */
class HotelQuoteRoomCloneCreatedEvent
{
    public $room;

    public function __construct(HotelQuoteRoom $room)
    {
        $this->room = $room;
    }
}
