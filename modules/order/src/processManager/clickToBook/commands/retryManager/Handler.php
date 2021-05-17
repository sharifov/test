<?php

namespace modules\order\src\processManager\clickToBook\commands\retryManager;

use modules\order\src\processManager\clickToBook\OrderProcessManager;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\clickToBook\RetryBookAppliedProducts;

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
        $retryBookAppliedProductsService = \Yii::createObject(RetryBookAppliedProducts::class);
        $retryBookAppliedProductsService->run($command->orderId);
    }
}
