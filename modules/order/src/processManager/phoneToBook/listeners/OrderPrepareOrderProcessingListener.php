<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\jobs\OrderPrepareJob;
use modules\order\src\processManager\events\BookedEvent;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;

/**
 * Class OrderPrepareOrderProcessingListener
 *
 * @property OrderProcessManagerRepository $managerRepository
 */
class OrderPrepareOrderProcessingListener
{
    private OrderProcessManagerRepository $managerRepository;

    public function __construct(OrderProcessManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function handle(BookedEvent $event): void
    {
        if (!$this->managerRepository->exist($event->orderId)) {
            return;
        }
        \Yii::$app->queue_job->push(new OrderPrepareJob($event->orderId));
    }
}
