<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteTrip;

/**
 * Class FlightQuoteSegmentDTO
 * @package modules\flight\src\useCases\flightQuote\create
 */
class FlightQuoteSegmentDTO
{
	public $flightQuoteId;
	public $flightQuoteTripId;
	public $departureDt;
	public $arrivalDt;
	public $stop;
	public $flightNumber;
	public $bookingClass;
	public $duration;
	public $departureAirportIata;
	public $departureAirportTerminal;
	public $arrivalAirportIata;
	public $arrivalAirportTerminal;
	public $operatingAirline;
	public $marketingAirline;
	public $airEquipType;
	public $marriageGroup;
	public $cabinClass;
	public $meal;
	public $fareCode;
	public $key;
	public $ticketId;
	public $recheckBaggage;
	public $mileage;

	/**
	 * FlightQuoteSegmentDTO constructor.
	 * @param FlightQuote $flightQuote
	 * @param FlightQuoteTrip $flightQuoteTrip
	 * @param array $segment
	 * @param mixed $ticketId
	 */
	public function __construct(FlightQuote $flightQuote, FlightQuoteTrip $flightQuoteTrip, array $segment, $ticketId = null)
	{
		$this->flightQuoteId = $flightQuote->fq_id;
		$this->flightQuoteTripId = $flightQuoteTrip->fqt_id;
		$this->departureDt = $segment['departureTime'];
		$this->arrivalDt = $segment['arrivalTime'];
		$this->stop = $segment['stop'];
		$this->flightNumber = $segment['flightNumber'];
		$this->bookingClass = $segment['bookingClass'];
		$this->duration = $segment['duration'];
		$this->departureAirportIata = $segment['departureAirportCode'];
		$this->departureAirportTerminal = $segment['departureAirportTerminal'] ?? '';
		$this->arrivalAirportIata = $segment['arrivalAirportCode'];
		$this->arrivalAirportTerminal = $segment['arrivalAirportTerminal'] ?? '';
		$this->operatingAirline = $segment['operatingAirline'];
		$this->marketingAirline = $segment['marketingAirline'];
		$this->airEquipType = $segment['airEquipType'];
		$this->marriageGroup = $segment['marriageGroup'] ?? '';
		$this->cabinClass = $segment['cabin'];
		$this->meal = $segment['meal'] ?? '';
		$this->fareCode = $segment['fareCode'];
		$this->ticketId = $ticketId;
		$this->recheckBaggage = $segment['recheckBaggage'] ? 1 : 0;
		$this->mileage = $segment['mileage'] ?? '';
		$this->key = '#'.$segment['flightNumber'].
			($segment['stop']>0?'('.$segment['stop'].')':'').
			$segment['departureAirportCode'].'-'.$segment['arrivalAirportCode'].' '.$segment['departureTime'];
	}
}