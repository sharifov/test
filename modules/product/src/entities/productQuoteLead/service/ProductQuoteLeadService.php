<?php

namespace modules\product\src\entities\productQuoteLead\service;

use modules\product\src\entities\productQuoteLead\ProductQuoteLead;
use modules\product\src\entities\productQuoteLead\repository\ProductQuoteLeadRepository;

/**
 * Class ProductQuoteLeadService
 * @package modules\product\src\entities\productQuoteLead\service
 *
 * @property ProductQuoteLeadRepository $repository
 */
class ProductQuoteLeadService
{
    private ProductQuoteLeadRepository $repository;

    public function __construct(ProductQuoteLeadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(int $productQuoteId, int $leadId): ProductQuoteLead
    {
        $productQuoteLead = ProductQuoteLead::create($productQuoteId, $leadId);
        $this->repository->save($productQuoteLead);
        return $productQuoteLead;
    }
}
