<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteSegment;

class FlightQuoteSegmentPaxBaggageDTO
{
	public $flightPaxCodeId;
	public $flightQuoteSegmentId;
	public $airlineCode;
	public $allowPieces;
	public $allowWeight;
	public $allowUnit;
	public $allowMaxWeight;
	public $allowMaxSize;

	/**
	 * FlightQuoteSegmentPaxBaggageDTO constructor.
	 * @param FlightQuoteSegment $flightQuoteSegment
	 * @param string $paxType
	 * @param array $baggage
	 */
	public function __construct(FlightQuoteSegment $flightQuoteSegment, string $paxType, array $baggage)
	{
		$this->flightPaxCodeId = FlightPax::getPaxId($paxType);
		$this->flightQuoteSegmentId = $flightQuoteSegment->fqs_id;
		$this->airlineCode = $baggage['airlineCode'] ?? null;
		$this->allowPieces = $baggage['allowPieces'] ?? null;
		$this->allowWeight = $baggage['allowWeight'] ?? null;
		$this->allowUnit = $baggage['allowUnit'] ?? null;
		$this->allowMaxWeight = $baggage['allowMaxWeight'] ?? null;
		$this->allowMaxSize = $baggage['allowMaxSize'] ?? null;
	}
}