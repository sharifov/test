<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\events\BookingOtherProductsEvent;
use modules\order\src\processManager\phoneToBook\jobs\StartBookingOtherProductsJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;

/**
 * Class StartBookingOtherProductsListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property Queue $queue
 */
class StartBookingOtherProductsListener
{
    private OrderProcessManagerRepository $repository;
    private Queue $queue;

    public function __construct(OrderProcessManagerRepository $repository, Queue $queue)
    {
        $this->repository = $repository;
        $this->queue = $queue;
    }

    public function handle(BookingOtherProductsEvent $event): void
    {
        if (!$this->repository->exist($event->getOrderId())) {
            return;
        }
        $this->queue->push(new StartBookingOtherProductsJob($event->getOrderId()));
    }
}
