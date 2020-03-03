<?php
namespace modules\hotel\src\helpers;

use sales\helpers\product\ProductQuoteHelper;

/**
 * Class HotelQuoteRoomPriceDTO
 * @package modules\hotel\src\helpers
 *
 * @property float $net
 * @property float $cancelAmount
 * @property float $serviceFeePercent
 * @property float $systemMarkup
 * @property float $agentMarkup
 * @property float $serviceFeeSum
 * @property float $sellingPrice
 */
class HotelQuoteRoomPriceDTO
{
	public $net;
	public $cancelAmount;
	public $serviceFeePercent;
	public $systemMarkup;
	public $agentMarkup;
	public $serviceFeeSum;
	public $sellingPrice;

	public function __construct(
		?float $price = null,
		?float $cancelAmount = null,
		?float $serviceFeePercent = null,
		?float $systemMarkup = null,
		?float $agentMarkup = null
	)
	{
		$this->net = $price;
		$this->cancelAmount = $cancelAmount;
		$this->serviceFeePercent = $serviceFeePercent;
		$this->systemMarkup = $systemMarkup;
		$this->agentMarkup = $agentMarkup;

		$this->serviceFeeSum = ProductQuoteHelper::roundPrice(($this->net + $this->systemMarkup + $this->agentMarkup) * $this->serviceFeePercent / 100);
		$this->sellingPrice = ProductQuoteHelper::roundPrice($this->net + $this->systemMarkup + $this->agentMarkup + $this->serviceFeeSum);
	}
}