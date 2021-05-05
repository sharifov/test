<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\jobs\OrderPrepareJob;
use modules\order\src\processManager\events\BookedEvent;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderPrepareOrderProcessingListener
 *
 * @property OrderProcessManagerRepository $managerRepository
 * @property Queue $queue
 */
class OrderPrepareOrderProcessingListener
{
    private OrderProcessManagerRepository $managerRepository;
    private Queue $queue;

    public function __construct(OrderProcessManagerRepository $managerRepository, Queue $queue)
    {
        $this->managerRepository = $managerRepository;
        $this->queue = $queue;
    }

    public function handle(BookedEvent $event): void
    {
        if (!$this->managerRepository->exist($event->orderId)) {
            return;
        }
        $this->queue->push(new OrderPrepareJob($event->orderId));
    }
}
