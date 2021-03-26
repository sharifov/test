<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\phoneToBook\events\BookingOtherProductsEvent;
use modules\order\src\processManager\phoneToBook\jobs\StartBookingOtherProductsJob;

class StartBookingOtherProductsListener
{
    public function handle(BookingOtherProductsEvent $event): void
    {
        \Yii::$app->queue_job->push(new StartBookingOtherProductsJob($event->getOrderId()));
    }
}
