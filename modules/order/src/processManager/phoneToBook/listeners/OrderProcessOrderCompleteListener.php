<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\jobs\OrderCompleteJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderProcessOrderCompleteListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property Queue $queue
 */
class OrderProcessOrderCompleteListener
{
    private OrderProcessManagerRepository $repository;
    private Queue $queue;

    public function __construct(OrderProcessManagerRepository $repository, Queue $queue)
    {
        $this->repository = $repository;
        $this->queue = $queue;
    }

    public function handle(OrderPaymentPaidEvent $event): void
    {
        $manager = $this->repository->get($event->orderId);

        if (!$manager) {
            return;
        }

        if (!$manager->isBooked()) {
            return;
        }

        $this->queue->push(new OrderCompleteJob($event->orderId));
    }
}
