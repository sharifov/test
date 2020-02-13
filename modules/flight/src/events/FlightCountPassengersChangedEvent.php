<?php

namespace modules\flight\src\events;

use modules\flight\models\Flight;

/**
 * Class FlightCountPassengersChangedEvent
 * @package modules\flight\src\events
 *
 * @property Flight $flight
 */
class FlightCountPassengersChangedEvent
{
	public $flight;

	/**
	 * @param Flight $flight
	 */
	public function __construct(Flight $flight)
	{
		$this->flight = $flight;
	}
}