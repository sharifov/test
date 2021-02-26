<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\processManager\events\BookingOtherProductsEvent;
use modules\order\src\processManager\jobs\StartBookingOtherProductsJob;

class StartBookingOtherProductsListener
{
    public function handle(BookingOtherProductsEvent $event): void
    {
        \Yii::$app->queue_job->push(new StartBookingOtherProductsJob($event->getOrderId()));
    }
}
