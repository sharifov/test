<?php

namespace modules\order\src\processManager\clickToBook\commands\flightProductProcessedSuccess;

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
        $manager = $this->repository->find($command->orderId);
        $manager->flightProductProcessed(new \DateTimeImmutable());
        $this->repository->save($manager);
    }
}
