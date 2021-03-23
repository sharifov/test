<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderRecalculateTotalPriceEvent;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\OrderManageService;

/**
 * Class OrderRecalculateTotalPriceListener
 * @package modules\order\src\listeners\order
 *
 * @property OrderRepository $orderRepository
 */
class OrderRecalculateTotalPriceListener
{
    /**
     * @var OrderManageService
     */
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(OrderRecalculateTotalPriceEvent $event)
    {
        $event->order->calculateTotalPrice();
        $this->repository->save($event->order);
    }
}
