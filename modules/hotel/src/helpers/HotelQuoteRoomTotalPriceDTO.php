<?php
namespace modules\hotel\src\helpers;

/**
 * Class HotelQuoteRoomTotalPriceDTO
 * @package modules\hotel\src\helpers
 *
 * @property float $net
 * @property float $systemMarkup
 * @property float $agentMarkup
 * @property float $sellingPrice
 * @property float $serviceFeeSum
 */
class HotelQuoteRoomTotalPriceDTO
{
	public $net;
	public $systemMarkup;
	public $agentMarkup;
	public $sellingPrice;
	public $serviceFeeSum;

	public function __construct(
		?float $net = null,
		?float $systemMarkup = null,
		?float $agentMarkup = null,
		?float $sellingPrice = null,
		?float $serviceFeeSum = null
	)
	{
		$this->net = $net;
		$this->systemMarkup = $systemMarkup;
		$this->agentMarkup = $agentMarkup;
		$this->sellingPrice = $sellingPrice;
		$this->serviceFeeSum = $serviceFeeSum;
	}
}