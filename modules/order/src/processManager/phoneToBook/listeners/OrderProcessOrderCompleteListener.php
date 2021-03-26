<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\jobs\OrderCompleteJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;

/**
 * Class OrderProcessOrderCompleteListener
 *
 * @property OrderProcessManagerRepository $repository
 */
class OrderProcessOrderCompleteListener
{
    private OrderProcessManagerRepository $repository;

    public function __construct(OrderProcessManagerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(OrderPaymentPaidEvent $event): void
    {
        $process = $this->repository->get($event->orderId);

        if (!$process) {
            return;
        }

        if (!$process->isBooked()) {
            return;
        }

        \Yii::$app->queue_job->push(new OrderCompleteJob($event->orderId));
    }
}
