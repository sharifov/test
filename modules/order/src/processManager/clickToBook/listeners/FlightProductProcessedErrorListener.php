<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\events\FlightProductProcessedErrorEvent;
use modules\order\src\processManager\clickToBook\jobs\FlightProductProcessedErrorJob;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;

/**
 * Class FlightProductProcessedErrorListener
 *
 * @property Queue $queue
 * @property OrderProcessManagerRepository $repository
 */
class FlightProductProcessedErrorListener
{
    private Queue $queue;
    private OrderProcessManagerRepository $repository;

    public function __construct(Queue $queue, OrderProcessManagerRepository $repository)
    {
        $this->queue = $queue;
        $this->repository = $repository;
    }

    public function handle(FlightProductProcessedErrorEvent $event): void
    {
        if (!$this->repository->exist($event->orderId)) {
            return;
        }
        $this->queue->push(new FlightProductProcessedErrorJob($event->orderId));
    }
}
