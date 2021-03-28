<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\events\CreatedEvent;
use modules\order\src\processManager\phoneToBook\jobs\StartBookingJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;

/**
 * Class StartBookingListener
 *
 * @property OrderProcessManagerRepository $repository
 */
class StartBookingListener
{
    private OrderProcessManagerRepository $repository;

    public function __construct(OrderProcessManagerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(CreatedEvent $event): void
    {
        if (!$this->repository->exist($event->getOrderId())) {
            return;
        }
        \Yii::$app->queue_job->push(new StartBookingJob($event->getOrderId()));
    }
}
