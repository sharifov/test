<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\processManager\events\CreatedEvent;
use modules\order\src\processManager\jobs\StartBookingJob;

class StartBookingListener
{
    public function handle(CreatedEvent $event): void
    {
        \Yii::$app->queue_job->push(new StartBookingJob($event->getOrderId()));
    }
}
