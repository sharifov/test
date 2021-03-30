<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\events\FlightProductProcessedErrorEvent;
use modules\order\src\processManager\clickToBook\jobs\FlightProductProcessedErrorJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class FlightProductProcessedErrorListener
 *
 * @property Queue $queue
 */
class FlightProductProcessedErrorListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(FlightProductProcessedErrorEvent $event): void
    {
        $this->queue->push(new FlightProductProcessedErrorJob($event->orderId));
    }
}
