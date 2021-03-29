<?php

namespace modules\order\src\listeners\order;

use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\jobs\OrderProcessingConfirmationJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderProcessingConfirmationListener
 *
 * @property Queue $queue
 */
class OrderProcessingConfirmationListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderProcessingEvent $event): void
    {
        $this->queue->push(new OrderProcessingConfirmationJob($event->order->or_id));
    }
}
