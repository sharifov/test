<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderCanceledEvent;
use modules\order\src\jobs\OrderCanceledConfirmationJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderCanceledConfirmationListener
 *
 * @property Queue $queue
 */
class OrderCanceledConfirmationListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderCanceledEvent $event): void
    {
        $this->queue->push(new OrderCanceledConfirmationJob($event->orderId));
    }
}
