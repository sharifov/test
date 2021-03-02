<?php

namespace modules\order\src\listeners\order;

use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\jobs\OrderProcessingHybridNotificationJob;

class OrderProcessingHybridNotificationListener
{
    public function handle(OrderProcessingEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderProcessingHybridNotificationJob($event->order->or_id));
    }
}
