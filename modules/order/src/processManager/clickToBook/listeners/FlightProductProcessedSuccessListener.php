<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\events\FlightProductProcessedSuccessEvent;
use modules\order\src\processManager\clickToBook\jobs\FlightProductProcessedSuccessJob;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;

/**
 * Class FlightProductProcessedSuccessListener
 *
 * @property Queue $queue
 * @property OrderProcessManagerRepository $repository
 */
class FlightProductProcessedSuccessListener
{
    private Queue $queue;
    private OrderProcessManagerRepository $repository;

    public function __construct(Queue $queue, OrderProcessManagerRepository $repository)
    {
        $this->queue = $queue;
        $this->repository = $repository;
    }

    public function handle(FlightProductProcessedSuccessEvent $event): void
    {
        if (!$this->repository->exist($event->orderId)) {
            return;
        }
        $this->queue->push(new FlightProductProcessedSuccessJob($event->orderId));
    }
}
