<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\jobs\OrderPrepareJob;
use modules\order\src\processManager\phoneToBook\events\BookedEvent;

class OrderPrepareOrderProcessingListener
{
    public function handle(BookedEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderPrepareJob($event->orderId));
    }
}
