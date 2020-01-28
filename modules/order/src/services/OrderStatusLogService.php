<?php

namespace modules\order\src\services;

use modules\order\src\entities\orderStatusLog\CreateDto;
use modules\order\src\entities\orderStatusLog\OrderStatusLog;
use modules\order\src\entities\orderStatusLog\OrderStatusLogRepository;

/**
 * Class OrderStatusLogService
 *
 * @property OrderStatusLogRepository $repository
 */
class OrderStatusLogService
{
    private $repository;

    public function __construct(OrderStatusLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function log(CreateDto $dto): void
    {
        if ($previous = $this->repository->getPrevious($dto->orderId)) {
            $previous->end();
            $this->repository->save($previous);
        }
        $log = OrderStatusLog::create($dto);
        $this->repository->save($log);
    }
}
