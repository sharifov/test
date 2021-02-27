<?php

namespace modules\order\src\useCase\orderComplete;

use modules\order\src\entities\order\OrderRepository;

/**
 * Class OrderCompleteService
 *
 * @property OrderRepository $repository
 */
class OrderCompleteService
{
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function complete(
        int $orderId,
        ?string $description,
        ?int $actionId,
        ?int $creatorId
    ): void {
        $order = $this->repository->find($orderId);
        $order->complete($description, $actionId, $creatorId);
        $this->repository->save($order);
    }
}
