<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\entities\order\events\OrderCancelable;
use modules\order\src\processManager\jobs\StopProcessManagerJob;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\queue\Queue;

/**
 * Class StopOrderProcessManagerListener
 *
 * @property Queue $queue
 */
class StopOrderProcessManagerListener
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderCancelable $event): void
    {
        if (OrderProcessManager::find()->byId($event->getId())->notStopped()->exists()) {
            $this->queue->push(new StopProcessManagerJob($event->getId()));
        }
    }
}
