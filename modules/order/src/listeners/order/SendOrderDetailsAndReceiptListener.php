<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCompleteEvent;

class SendOrderDetailsAndReceiptListener
{
    public function handle(OrderCompleteEvent $event): void
    {
        \Yii::info([
            'message' => 'Send order details',
            'orderId' => $event->orderId,
        ], 'info\SendOrderDetailsAndReceipt');
    }
}
