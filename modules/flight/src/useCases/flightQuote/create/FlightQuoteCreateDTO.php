<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuoteHelper;
use sales\auth\Auth;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteCreateDTO
 * @package modules\flight\src\useCases\flightQuote\create
 */
class FlightQuoteCreateDTO
{
	public $flightId;
	public $sourceId;
	public $productQuoteId;
	public $hashKey;
	public $serviceFeePercent;
	public $recordLocator;
	public $gds;
	public $gdsPcc;
	public $gdsOfferId;
	public $typeId;
	public $cabinClass;
	public $tripTypeId;
	public $mainAirline;
	public $fareType;
	public $createdUserId;
	public $createdExpertId;
	public $createdExpertName;
	public $reservationDump;
	public $pricingInfo;
	public $originSearchData;
	public $lastTicketDate;
	public $requestHash;

	/**
	 * FlightQuoteCreateDTO constructor.
	 * @param Flight $flight
	 * @param array $quote
	 * @param ProductQuote $productQuote
	 */
	public function __construct(Flight $flight, ProductQuote $productQuote, array $quote, int $userId)
	{
		$this->flightId = $flight->fl_id;
		$this->sourceId = null;
		$this->productQuoteId = $productQuote->pq_id;
		$this->hashKey = FlightQuoteHelper::generateHashQuoteKey($quote['key']);
		$this->serviceFeePercent = null;
		$this->recordLocator = null;
		$this->gds = $quote['gds'];
		$this->gdsPcc = $quote['pcc'];
		$this->gdsOfferId = $quote['gdsOfferId'] ?? null;
		$this->typeId = $flight->originalQuoteExist() ? FlightQuote::TYPE_ALTERNATIVE : FlightQuote::TYPE_BASE;
		$this->cabinClass = $flight->fl_cabin_class;
		$this->tripTypeId = $flight->fl_trip_type_id;
		$this->mainAirline = $quote['validatingCarrier'];
		$this->fareType = FlightQuote::getFareTypeId($quote['fareType']);
		$this->createdUserId = $userId;
		$this->createdExpertId = null;
		$this->createdExpertName = null;
		$this->reservationDump = FlightQuoteHelper::getItineraryDump($quote);
		$this->pricingInfo = json_encode($quote['prices']);
		$this->originSearchData = json_encode($quote);
		$this->lastTicketDate = $quote['prices']['lastTicketDate'];
		$this->requestHash = $flight->fl_request_hash_key;
	}
}