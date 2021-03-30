<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\jobs\CheckOrderIsPaymentJob;
use modules\order\src\processManager\events\FlightProductProcessedEvent;
use modules\order\src\processManager\queue\Queue;

/**
 * Class CheckOrderIsPaymentListener
 *
 * @property Queue $queue
 */
class CheckOrderIsPaymentListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(FlightProductProcessedEvent $event): void
    {
        $this->queue->push(new CheckOrderIsPaymentJob($event->orderId));
    }
}
