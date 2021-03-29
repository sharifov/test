<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\jobs\OrderCompletedHybridNotificationJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderCompletedHybridNotificationListener
 *
 * @property Queue $queue
 */
class OrderCompletedHybridNotificationListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderCompletedEvent $event): void
    {
        $this->queue->push(new OrderCompletedHybridNotificationJob($event->orderId));
    }
}
