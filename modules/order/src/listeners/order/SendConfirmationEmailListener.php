<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCompletedEvent;

class SendConfirmationEmailListener
{
    public function handle(OrderCompletedEvent $event): void
    {
        \Yii::info([
            'message' => 'Send email',
            'orderId' => $event->orderId,
        ], 'info\SendConfirmationEmail');
    }
}
