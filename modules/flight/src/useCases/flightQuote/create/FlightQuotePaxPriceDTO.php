<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;

class FlightQuotePaxPriceDTO
{
	public $flightQuoteId;
	public $flightPaxCodeId;
	public $fare;
	public $tax;
	public $systemMarkUp;
	public $agentMarkUp;
	public $originFare;
	public $originCurrency;
	public $originTax;
	public $clientCurrency;
	public $clientFare;
	public $clientTax;

	/**
	 * FlightQuotePaxPriceDTO constructor.
	 * @param FlightQuote $flightQuote
	 * @param ProductQuote $productQuote
	 * @param array $pax
	 * @param string $paxType
	 * @param array $quote
	 */
	public function __construct(FlightQuote $flightQuote, ProductQuote $productQuote, array $pax, string $paxType, array $quote)
	{
		$this->flightQuoteId = $flightQuote->fq_id;
		$this->flightPaxCodeId = FlightPax::getPaxId($paxType);
		$this->fare = (float)($pax['baseFare'] ?? null);
		$this->tax = (float)($pax['tax'] ?? null);
		$this->systemMarkUp = (float)($pax['markup'] ?? 0);
		$this->agentMarkUp = (float)($pax['markup'] ?? 0);
		$this->originFare = $pax['oBaseFare']['amount'] ?? null;
		$this->originCurrency = $productQuote->pq_origin_currency;
		$this->originTax = $pax['baseTax'] ?? null;
		$this->clientCurrency = $productQuote->pq_client_currency;
		$this->clientFare = null;
		$this->clientTax = null;
	}

}