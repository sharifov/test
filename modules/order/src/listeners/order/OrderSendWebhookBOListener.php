<?php

namespace modules\order\src\listeners\order;

use modules\order\src\events\OrderUpdateEvent;
use modules\order\src\jobs\OrderWebhookJob;

class OrderSendWebhookBOListener
{
    public function handle(OrderUpdateEvent $event): void
    {
        \Yii::$app->queue_job->delay(10)->push(new OrderWebhookJob($event->orderId));
    }
}
