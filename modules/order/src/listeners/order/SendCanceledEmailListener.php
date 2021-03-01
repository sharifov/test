<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCanceledEvent;

class SendCanceledEmailListener
{
    public function handle(OrderCanceledEvent $event): void
    {
        \Yii::info([
            'message' => 'Send canceled email',
            'orderId' => $event->orderId,
        ], 'info\SendCanceledEmail');
    }
}
