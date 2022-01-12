<?php

namespace src\model\user\entity\profit\event;

use modules\order\src\entities\order\Order;

/**
 * Class UserProfitCalculateByOrderTipsUserProfits
 * @package src\model\user\entity\profit\event
 *
 * @property Order $order
 */
class UserProfitCalculateByOrderTipsUserProfitsEvent
{
    /**
     * @var Order
     */
    public $order;

    /**
     * UserProfitCalculateByOrderTipsUserProfits constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
