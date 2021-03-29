<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\jobs\CompleteOrderJob;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\events\BookedEvent;
use modules\order\src\processManager\Queue;

/**
 * Class CompleteOrderListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property Queue $queue
 */
class CompleteOrderListener
{
    private OrderProcessManagerRepository $repository;
    private Queue $queue;

    public function __construct(OrderProcessManagerRepository $repository, Queue $queue)
    {
        $this->repository = $repository;
        $this->queue = $queue;
    }

    public function handle(BookedEvent $event): void
    {
        $manager = $this->repository->exist($event->getOrderId());

        if (!$manager) {
            return;
        }

        $this->queue->push(new CompleteOrderJob($event->getOrderId()));
    }
}
