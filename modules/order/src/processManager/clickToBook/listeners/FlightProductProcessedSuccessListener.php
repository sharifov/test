<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\events\FlightProductProcessedSuccessEvent;
use modules\order\src\processManager\clickToBook\jobs\FlightProductProcessedSuccessJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class FlightProductProcessedSuccessListener
 *
 * @property Queue $queue
 */
class FlightProductProcessedSuccessListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(FlightProductProcessedSuccessEvent $event): void
    {
        $this->queue->push(new FlightProductProcessedSuccessJob($event->orderId));
    }
}
