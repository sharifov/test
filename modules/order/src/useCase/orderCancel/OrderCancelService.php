<?php

namespace modules\order\src\useCase\orderCancel;

use modules\order\src\entities\order\OrderRepository;

/**
 * Class OrderCancelService
 *
 * @property OrderRepository $repository
 */
class OrderCancelService
{
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function cancel(
        int $orderId,
        ?string $description,
        ?int $actionId,
        ?int $creatorId
    ): void {
        $order = $this->repository->find($orderId);
        $order->cancel($description, $actionId, $creatorId);
        $this->repository->save($order);
    }
}
