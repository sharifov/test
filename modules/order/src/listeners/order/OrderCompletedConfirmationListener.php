<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\jobs\OrderCompletedConfirmationJob;

class OrderCompletedConfirmationListener
{
    public function handle(OrderCompletedEvent $event): void
    {
        \Yii::$app->queue_job->push(new OrderCompletedConfirmationJob($event->orderId));
    }
}
