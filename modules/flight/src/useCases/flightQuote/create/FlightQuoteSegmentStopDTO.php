<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\FlightQuoteSegment;

/**
 * Class FlightQuoteSegmentStop
 * @package modules\flight\src\useCases\flightQuote\create
 */
class FlightQuoteSegmentStopDTO
{
	public $quoteSegmentId;
	public $locationIata;
	public $equipment;
	public $elapsedTime;
	public $duration;
	public $departureDt;
	public $arrivalDt;

	/**
	 * FlightQuoteSegmentStop constructor.
	 * @param FlightQuoteSegment $flightQuoteSegment
	 * @param array $stop
	 */
	public function __construct(FlightQuoteSegment $flightQuoteSegment, array $stop)
	{
		$this->quoteSegmentId = $flightQuoteSegment->fqs_id;
		$this->locationIata = $stop['locationCode'];
		$this->departureDt = $stop['departureDateTime'];
		$this->arrivalDt = $stop['arrivalDateTime'];
		$this->duration = $stop['duration'] ?? null;
		$this->elapsedTime = $stop['elapsedTime'] ?? null;
		$this->equipment = $stop['equipment'] ?? null;
	}
}