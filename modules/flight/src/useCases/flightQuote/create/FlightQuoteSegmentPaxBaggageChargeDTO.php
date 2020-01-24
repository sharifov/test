<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteSegment;

class FlightQuoteSegmentPaxBaggageChargeDTO
{
	public $flightPaxCodeId;
	public $flightQuoteSegmentId;
	public $firstPiece;
	public $lastPiece;
	public $originPrice;
	public $originCurrency;
	public $price;
	public $clientPrice;
	public $clientCurrency;
	public $maxWeight;
	public $maxSize;

	/**
	 * FlightQuoteSegmentPaxBaggageChargeDTO constructor.
	 * @param FlightQuoteSegment $flightQuoteSegment
	 * @param string $paxType
	 * @param array $charge
	 */
	public function __construct(FlightQuoteSegment $flightQuoteSegment, string $paxType, array $charge)
	{
		$this->flightPaxCodeId = FlightPax::getPaxId($paxType);
		$this->flightQuoteSegmentId = $flightQuoteSegment->fqs_id;
		$this->firstPiece = $charge['firstPiece'] ?? null;
		$this->lastPiece = $charge['lastPiece'] ?? null;
		$this->originPrice = null;
		$this->originCurrency = null;
		$this->price = $charge['price'] ?? null;
		$this->clientPrice = null;
		$this->clientCurrency = null;
		$this->maxWeight = $charge['maxWeight'] ?? null;
		$this->maxSize = $charge['maxSize'] ?? null;
	}
}