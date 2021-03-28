<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\events\BookingOtherProductsEvent;
use modules\order\src\processManager\phoneToBook\jobs\StartBookingOtherProductsJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;

/**
 * Class StartBookingOtherProductsListener
 *
 * @property OrderProcessManagerRepository $repository
 */
class StartBookingOtherProductsListener
{
    private OrderProcessManagerRepository $repository;

    public function __construct(OrderProcessManagerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(BookingOtherProductsEvent $event): void
    {
        if (!$this->repository->exist($event->getOrderId())) {
            return;
        }
        \Yii::$app->queue_job->push(new StartBookingOtherProductsJob($event->getOrderId()));
    }
}
