<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\jobs\CheckOrderIsPaymentJob;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\events\FlightProductProcessedEvent;
use modules\order\src\processManager\queue\Queue;

/**
 * Class CheckOrderIsPaymentListener
 *
 * @property Queue $queue
 * @property OrderProcessManagerRepository $repository
 */
class CheckOrderIsPaymentListener
{
    private Queue $queue;
    private OrderProcessManagerRepository $repository;

    public function __construct(Queue $queue, OrderProcessManagerRepository $repository)
    {
        $this->queue = $queue;
        $this->repository = $repository;
    }

    public function handle(FlightProductProcessedEvent $event): void
    {
        if (!$this->repository->exist($event->orderId)) {
            return;
        }
        $this->queue->push(new CheckOrderIsPaymentJob($event->orderId));
    }
}
