<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use sales\helpers\product\ProductQuoteHelper;

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
	public $cnt;

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

		$this->originFare = $pax['baseFare'] ?? null;
		$this->originTax = $pax['baseTax'] ?? null;
		$this->originCurrency = $productQuote->pq_origin_currency;

		$this->fare = ProductQuoteHelper::calcSystemPrice((float) $this->originFare, $pax['oBaseFare']['currency'] ?? $quote['currency']);
		$this->tax =  ProductQuoteHelper::calcSystemPrice((float) $this->originTax, $pax['oBaseTax']['currency'] ?? $quote['currency']);

		$this->clientFare = ProductQuoteHelper::calcClientPrice($this->fare, $productQuote->pqProduct);
		$this->clientTax = ProductQuoteHelper::calcClientPrice($this->tax, $productQuote->pqProduct);
		$this->clientCurrency = $productQuote->pq_client_currency;

		$this->systemMarkUp = (ProductQuoteHelper::calcSystemPrice($pax['markup'], $pax['oMarkup']['currency'] ?? $quote['currency']));
		$this->agentMarkUp = 0;
		$this->cnt = $pax['cnt'];
	}

}