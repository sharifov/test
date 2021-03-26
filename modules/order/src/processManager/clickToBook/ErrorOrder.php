<?php

namespace modules\order\src\processManager;

use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;

/**
 * Class ErrorOrder
 *
 * @property OrderRepository $orderRepository
 * @property OrderProcessManagerRepository $managerRepository
 */
class ErrorOrder
{
    private OrderRepository $orderRepository;
    private OrderProcessManagerRepository $managerRepository;

    public function __construct(OrderRepository $orderRepository, OrderProcessManagerRepository $managerRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->managerRepository = $managerRepository;
    }

    public function error(int $orderId): void
    {
        $order = $this->orderRepository->find($orderId);
        $order->error('Error on ClickToBook auto processing', OrderStatusAction::AUTO_PROCESSING, null);
        $this->orderRepository->save($order);

        $manager = $this->managerRepository->find($orderId);
        $manager->failed(new \DateTimeImmutable());
        $this->managerRepository->save($manager);
    }
}
