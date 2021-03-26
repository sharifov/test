<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\phoneToBook\events\CreatedEvent;
use modules\order\src\processManager\phoneToBook\jobs\StartBookingJob;

class StartBookingListener
{
    public function handle(CreatedEvent $event): void
    {
        \Yii::$app->queue_job->push(new StartBookingJob($event->getOrderId()));
    }
}
