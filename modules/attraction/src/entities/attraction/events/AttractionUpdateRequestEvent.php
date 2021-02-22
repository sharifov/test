<?php

namespace modules\attraction\src\entities\attraction\events;

use modules\attraction\models\Attraction;

/**
 * Class AttractionUpdateRequestEvent
 *
 * @property Attraction $attraction
 */
class AttractionUpdateRequestEvent
{
    public $attraction;

    public function __construct(Attraction $attraction)
    {
        $this->attraction = $attraction;
    }
}
