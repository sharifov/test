<?php

namespace modules\order\src\entities\order\events;

/**
 * Class OrderRecalculateProfitAmountEvent
 * @package modules\order\src\entities\order\events
 */
class OrderRecalculateProfitAmountEvent
{
    public $orders;

    /**
     * OrderRecalculateProfitAmountEvent constructor.
     * @param array $orders [Order]
     */
    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }
}
