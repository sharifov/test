<?php

namespace sales\model\user\entity\profit\event;

use modules\order\src\entities\order\Order;

/**
 * Class UserProfitCalculateByOrderUserProfitEvent
 * @package sales\model\user\entity\profit\event
 *
 * @property Order $order
 * @property int $userProfitType
 */
class UserProfitCalculateByOrderUserProfitEvent
{
	public $order;

	/**
	 * UserProfitCalculateByOrderUserProfitEvent constructor.
	 * @param Order $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
	}
}