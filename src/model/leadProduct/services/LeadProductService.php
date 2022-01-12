<?php

namespace src\model\leadProduct\services;

use src\model\leadProduct\entity\LeadProduct;
use src\model\leadProduct\entity\LeadProductRepository;

/**
 * Class LeadProductService
 *
 * @property LeadProductRepository $repository
 */
class LeadProductService
{
    private LeadProductRepository $repository;

    public function __construct(LeadProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(int $leadId, int $productId): void
    {
        $leadOrder = LeadProduct::create($leadId, $productId);
        $this->repository->save($leadOrder);
    }
}
