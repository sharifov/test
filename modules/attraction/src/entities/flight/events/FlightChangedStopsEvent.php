<?php

namespace modules\flight\src\entities\flight\events;

use modules\flight\models\Flight;

/**
 * Class FlightChangedStopsEvent
 *
 * @property Flight $flight
 */
class FlightChangedStopsEvent
{
    public $flight;

    public function __construct(Flight $flight)
    {
        $this->flight = $flight;
    }
}
