<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCanceledEvent;
use modules\order\src\jobs\OrderCanceledHybridNotificationJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderCanceledHybridNotificationListener
 *
 * @property Queue $queue
 */
class OrderCanceledHybridNotificationListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderCanceledEvent $event): void
    {
        $this->queue->push(new OrderCanceledHybridNotificationJob($event->orderId));
    }
}
