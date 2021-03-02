<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\jobs\OrderCompletedHybridNotificationJob;

class OrderCompletedHybridNotificationListener
{
    public function handle(OrderCompletedEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderCompletedHybridNotificationJob($event->orderId));
    }
}
