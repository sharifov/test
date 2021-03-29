<?php

namespace modules\order\src\listeners\order;

use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\jobs\OrderProcessingHybridNotificationJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderProcessingHybridNotificationListener
 *
 * @property Queue $queue
 */
class OrderProcessingHybridNotificationListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderProcessingEvent $event): void
    {
        $this->queue->push(new OrderProcessingHybridNotificationJob($event->order->or_id));
    }
}
