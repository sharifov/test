<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderStatusable;

class OrderLogStatusListener
{
    public function handle(OrderStatusable $event): void
    {
        \Yii::info([
            'message' => 'Order transfer to ' . $event->getStatusName(),
            'orderId' => $event->getOrderId(),
            'date' => $event->getDate(),
        ], 'info\Order::OrderLogStatusListener');
    }
}
