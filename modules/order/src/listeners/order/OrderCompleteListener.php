<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\jobs\OrderCompleteJob;

class OrderCompleteListener
{
    public function handle(OrderPaymentPaidEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderCompleteJob($event->orderId));
    }
}
