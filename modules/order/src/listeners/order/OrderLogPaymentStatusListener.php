<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderPaymentStatusable;

class OrderLogPaymentStatusListener
{
    public function handle(OrderPaymentStatusable $event): void
    {
        \Yii::info([
            'message' => 'Order transfer to Payment ' . $event->getStatusName(),
            'orderId' => $event->getOrderId(),
            'date' => $event->getDate(),
        ], 'info\Order::OrderLogPaymentStatusListener');
    }
}
