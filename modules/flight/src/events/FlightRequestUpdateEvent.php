<?php

namespace modules\flight\src\events;

use modules\flight\models\Flight;

/**
 * Class FlightRequestUpdateEvent
 * @package modules\flight\src\events
 *
 * @property Flight $flight
 */
class FlightRequestUpdateEvent
{
	public const EVENT_KEY = 'FlightRequestUpdate';

	/**
	 * @var Flight
	 */
	public $flight;

	/**
	 * @param Flight $flight
	 */
	public function __construct(Flight $flight)
	{
		$this->flight = $flight;
	}
}