<?php
namespace modules\flight\src\helpers;

/**
 * Class FlightQuotePriceDataDTO
 * @package modules\flight\src\helpers
 *
 * @property FlightQuotePaxPriceDataDTO[] $prices
 * @property FlightQuoteTotalPriceDTO $total
 * @property $serviceFeePercent float
 * @property $serviceFee float;
 * @property $processingFee float
 */
class FlightQuotePriceDataDTO
{
	/**
	 * @var $prices FlightQuotePaxPriceDataDTO[]
	 */
	public $prices;

	/**
	 * @var FlightQuoteTotalPriceDTO
	 */
	public $total;

	/**
	 * @var float
	 */
	public $serviceFeePercent;

	/**
	 * @var float
	 */
	public $serviceFee;

	/**
	 * @var float
	 */
	public $processingFee;
}