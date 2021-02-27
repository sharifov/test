<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCompletedEvent;

class SendOrderDetailsAndReceiptListener
{
    public function handle(OrderCompletedEvent $event): void
    {
        \Yii::info([
            'message' => 'Send order details',
            'orderId' => $event->orderId,
        ], 'info\SendOrderDetailsAndReceipt');
    }
}
