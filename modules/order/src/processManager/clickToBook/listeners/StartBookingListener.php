<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\jobs\StartAutoProcessingJob;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\events\CreatedEvent;
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
        $manager = $this->repository->exist($event->getOrderId());

        if (!$manager) {
            return;
        }

        $this->queue->push(new StartAutoProcessingJob($event->getOrderId()));
    }
}
