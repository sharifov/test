<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\processManager\events\FlightQuoteBookedEvent;
use modules\order\src\processManager\jobs\AfterBookedFlightJob;

class AfterBookedFlightListener
{
    public function handle(FlightQuoteBookedEvent $event): void
    {
        \Yii::$app->queue_job->push(new AfterBookedFlightJob($event->quoteId));
    }
}
