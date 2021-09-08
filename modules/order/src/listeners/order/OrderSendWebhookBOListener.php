<?php

namespace modules\order\src\listeners\order;

use modules\order\src\events\OrderUpdateEvent;
use modules\order\src\jobs\OrderWebhookJob;

class OrderSendWebhookBOListener
{
    public function handle(OrderUpdateEvent $event): void
    {
        // TODO: OrderUpdateEvent JOB
//        $delayJob = 10;
//        $job = new OrderWebhookJob($event->orderId);
//        $job->delayJob = $delayJob;
//        \Yii::$app->queue_job->delay($delayJob)->push($job);
    }
}
