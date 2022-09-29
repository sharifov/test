<?php

namespace src\model\leadOrder\services;

use src\model\leadOrder\entity\LeadOrder;
use src\model\leadOrder\entity\LeadOrderRepository;

/**
 * Class LeadOrderService
 *
 * @property LeadOrderRepository $repository
 */
class LeadOrderService
{
    private LeadOrderRepository $repository;

    public function __construct(LeadOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(int $leadId, int $orderId, ?int $createdUserId = null): void
    {
        $leadOrder = LeadOrder::create($leadId, $orderId, $createdUserId);
        $this->repository->save($leadOrder);
    }
}
