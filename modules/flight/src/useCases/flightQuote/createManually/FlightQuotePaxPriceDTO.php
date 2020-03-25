<?php
namespace modules\flight\src\useCases\createManually;

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
}