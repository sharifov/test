<?php

namespace modules\order\src\listeners\order;

use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\jobs\OrderProcessingConfirmationJob;

class OrderProcessingConfirmationListener
{
    public function handle(OrderProcessingEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderProcessingConfirmationJob($event->order->or_id));
    }
}
