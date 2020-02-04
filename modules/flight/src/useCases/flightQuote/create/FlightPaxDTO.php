<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\Flight;

class FlightPaxDTO
{
	public $flightId;
	public $paxId;
	public $paxType;
	public $firstName;
	public $lastName;
	public $middleName;
	public $dob;

	/**
	 * FlightPaxDTO constructor.
	 * @param Flight $flight
	 * @param string $paxType
	 */
	public function __construct(Flight $flight, string $paxType)
	{
		$this->flightId = $flight->fl_id;
		$this->paxId = null;
		$this->paxType = $paxType;
		$this->firstName = null;
		$this->lastName = null;
		$this->middleName = null;
		$this->dob = null;
	}
}