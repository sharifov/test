<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\events\CreatedEvent;
use modules\order\src\processManager\phoneToBook\jobs\StartBookingJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;

/**
 * Class StartBookingListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property Queue $queue
 */
class StartBookingListener
{
    private OrderProcessManagerRepository $repository;
    private Queue $queue;

    public function __construct(OrderProcessManagerRepository $repository, Queue $queue)
    {
        $this->repository = $repository;
        $this->queue = $queue;
    }

    public function handle(CreatedEvent $event): void
    {
        if (!$this->repository->exist($event->getOrderId())) {
            return;
        }
        $this->queue->push(new StartBookingJob($event->getOrderId()));
    }
}
