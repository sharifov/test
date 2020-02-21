<?php
namespace modules\order\src\entities\order\events;

use modules\order\src\entities\order\Order;

/**
 * Class OrderUserProfitUpdateProfitAmountEvent
 * @package modules\order\src\entities\order\events
 *
 * @property Order $order;
 */
class OrderUserProfitUpdateProfitAmountEvent
{
	public $order;

	public function __construct(Order $order)
	{
		$this->order = $order;
	}
}