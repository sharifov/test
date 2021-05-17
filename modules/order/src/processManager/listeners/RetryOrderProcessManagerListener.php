<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\entities\order\Order;
use modules\order\src\processManager;

class RetryOrderProcessManagerListener
{
    private processManager\queue\Queue $queue;

    public function __construct(processManager\queue\Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(processManager\events\RetryEvent $event): void
    {
        $order = Order::findOne($event->getOrderId());

        if (!$order) {
            return;
        }

        if ($order->isClickToBook()) {
            $this->queue->push(new processManager\clickToBook\jobs\RetryOrderProcessManagerJob($event->getOrderId()));
            return;
        }
    }
}
