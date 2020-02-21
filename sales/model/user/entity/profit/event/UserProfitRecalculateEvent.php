<?php

namespace sales\model\user\entity\profit\event;

use modules\order\src\entities\order\Order;

/**
 * Class UserProfitRecalculateEvent
 * @package sales\model\user\entity\profit\event
 *
 * @property Order $order
 */
class UserProfitRecalculateEvent
{
	public $order;

	/**
	 * UserProfitRecalculateEvent constructor.
	 * @param Order $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
	}
}