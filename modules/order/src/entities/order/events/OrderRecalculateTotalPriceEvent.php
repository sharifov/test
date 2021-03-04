<?php

namespace modules\order\src\entities\order\events;

use modules\order\src\entities\order\Order;

/**
 * Class OrderRecalculateTotalPriceEvent
 * @package modules\order\src\entities\order\events
 *
 * @property Order $order
 */
class OrderRecalculateTotalPriceEvent
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
