<?php

namespace modules\hotel\src\entities\hotel\events;

use modules\hotel\models\Hotel;

/**
 * Class HotelUpdateRequestEvent
 *
 * @property Hotel $hotel
 */
class HotelUpdateRequestEvent
{
    public $hotel;

    public function __construct(Hotel $hotel)
    {
        $this->hotel = $hotel;
    }
}
