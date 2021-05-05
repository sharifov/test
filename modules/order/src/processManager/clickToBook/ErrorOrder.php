<?php

namespace modules\order\src\processManager\clickToBook;

use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;
use sales\services\TransactionManager;

/**
 * Class ErrorOrder
 *
 * @property OrderRepository $orderRepository
 * @property OrderProcessManagerRepository $managerRepository
 * @property TransactionManager $transactionManager
 */
class ErrorOrder
{
    private OrderRepository $orderRepository;
    private OrderProcessManagerRepository $managerRepository;
    private TransactionManager $transactionManager;

    public function __construct(
        OrderRepository $orderRepository,
        OrderProcessManagerRepository $managerRepository,
        TransactionManager $transactionManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->managerRepository = $managerRepository;
        $this->transactionManager = $transactionManager;
    }

    public function error(int $orderId, string $description): void
    {
        $this->transactionManager->wrap(function () use ($orderId, $description) {
            $order = $this->orderRepository->find($orderId);
            $order->error($description, OrderStatusAction::AUTO_PROCESSING, null);
            $this->orderRepository->save($order);

            $manager = $this->managerRepository->find($orderId);
            $manager->stop(new \DateTimeImmutable());
            $this->managerRepository->save($manager);
        });
    }
}
