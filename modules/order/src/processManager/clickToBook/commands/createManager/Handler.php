<?php

namespace modules\order\src\processManager\clickToBook\commands\createManager;

use modules\order\src\processManager\clickToBook\OrderProcessManager;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;

/**
 * Class Handler
 *
 * @property OrderProcessManagerRepository $repository
 */
class Handler
{
    private OrderProcessManagerRepository $repository;

    public function __construct(OrderProcessManagerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Command $command): void
    {
        if ($this->repository->exist($command->orderId)) {
            throw new \DomainException('ClickToBook Order Process Manager is already exist. Id: ' . $command->orderId);
        }

        $manager = OrderProcessManager::create($command->orderId, new \DateTimeImmutable());
        $this->repository->save($manager);
    }
}
