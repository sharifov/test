<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCanceledEvent;
use modules\order\src\jobs\OrderCanceledHybridNotificationJob;

class OrderCanceledHybridNotificationListener
{
    public function handle(OrderCanceledEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderCanceledHybridNotificationJob($event->orderId));
    }
}
