<?php
namespace modules\flight\src\helpers;

/**
 * Class FlightQuotePaxPriceDataDTO
 * @package modules\flight\src\helpers
 *
 * @property $fare
 * @property $taxes
 * @property $net
 * @property $tickets
 * @property $markUp
 * @property $extraMarkUp
 * @property $serviceFee
 * @property $selling
 * @property $serviceFeeSum
 * @property $clientSelling
 * @property $paxCode
 * @property $paxCodeId
 */
class FlightQuotePaxPriceDataDTO
{
	public $fare = 0;
	public $taxes = 0;
	public $net = 0;
	public $tickets = 0;
	public $markUp = 0;
	public $extraMarkUp = 0;
	public $serviceFee = 0;
	public $selling = 0;
	public $serviceFeeSum = 0;
	public $clientSelling = 0;
	public $paxCodeId;
	public $paxCode;
}