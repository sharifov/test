<?php

namespace modules\order\src\listeners\order;

use modules\order\src\jobs\OrderPrepareJob;
use modules\order\src\processManager\events\BookedEvent;

class OrderPrepareListener
{
    public function handle(BookedEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderPrepareJob($event->orderId));
    }
}
