<?php

namespace modules\order\src\processManager\clickToBook\commands\completeOrder;

use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;

/**
 * Class Handler
 *
 * @property OrderRepository $repository
 */
class Handler
{
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        $order = $this->repository->find($command->orderId);
        $order->complete('ClickToBook Auto Process successfully finished', OrderStatusAction::AUTO_PROCESSING, null);
        $this->repository->save($order);
    }
}
