<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCompleteEvent;

class SendConfirmationEmailListener
{
    public function handle(OrderCompleteEvent $event): void
    {
        \Yii::info([
            'message' => 'Send email',
            'orderId' => $event->orderId,
        ], 'info\SendConfirmationEmail');
    }
}
