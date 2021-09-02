<?php

namespace modules\product\src\entities\productQuoteChange\events;

class ProductQuoteChangeDecisionRefundEvent implements ProductQuoteChangeDecisionable
{
    public int $productQuoteChangeId;
    public int $productQuoteId;

    public function __construct(int $productQuoteChangeId, int $productQuoteId)
    {
        $this->productQuoteChangeId = $productQuoteChangeId;
        $this->productQuoteId = $productQuoteId;
    }

    public function getId(): int
    {
        return $this->productQuoteChangeId;
    }
}
