<?php

namespace modules\flight\src\entities\flight\events;

use modules\flight\models\Flight;

/**
 * Class FlightChangedDelayedChargeEvent
 *
 * @property Flight $flight
 */
class FlightChangedDelayedChargeEvent
{
    public $flight;

    public function __construct(Flight $flight)
    {
        $this->flight = $flight;
    }
}
