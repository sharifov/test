<?php
namespace modules\flight\src\helpers;

/**
 * Class FlightQuoteTotalPriceDTO
 * @package modules\flight\src\helperss
 *
 * @property $tickets
 * @property $net
 * @property $markUp
 * @property $extraMarkUp
 * @property $selling
 * @property $serviceFeeSum
 * @property $clientSelling
 */
class FlightQuoteTotalPriceDTO
{
	public $tickets = 0;
	public $net = 0;
	public $markUp = 0;
	public $extraMarkUp = 0;
	public $selling = 0;
	public $serviceFeeSum = 0;
	public $clientSelling = 0;
}