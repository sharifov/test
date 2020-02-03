<?php

namespace modules\flight\src\dto\itineraryDump;

/**
 * Class ItineraryDumpDTO
 * @package modules\flight\src\dto\itineraryDump
 */
class ItineraryDumpDTO
{
	public $airlineCode;
	public $departureAirportCode;
	public $destinationAirportCode;
	public $departureTime;
	public $arrivalTime;
	public $flightNumber;
	public $mainAirlineCode;
	public $duration;
	public $bookingClass;
	public $cabin;
	public $operationAirlineCode;
	public $aircraftCode;
	public $aircraftModel;
	public $airlineRecordLocator;
	public $baggageAllowanceNumber;
	public $statusCode;

	/**
	 * ItineraryDumpDTO constructor.
	 * @param array $segment
	 */
	public function __construct(array $segment)
	{
		$this->departureTime = $segment['departureTime'];
		$this->arrivalTime = $segment['arrivalTime'];
		$this->airlineCode = $segment['marketingAirline'];
		$this->flightNumber = $segment['flightNumber'];
		$this->bookingClass = $segment['bookingClass'];
		$this->departureAirportCode = $segment['departureAirportCode'];
		$this->destinationAirportCode = $segment['arrivalAirportCode'];
		$this->operationAirlineCode = $segment['operatingAirline'] !== $segment['marketingAirline'] ? $segment['operatingAirline'] : null;
	}
}