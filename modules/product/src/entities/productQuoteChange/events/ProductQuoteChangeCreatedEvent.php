<?php

namespace modules\product\src\entities\productQuoteChange\events;

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;

/**
 * Class ProductQuoteChangeCreatedEvent
 *
 * @property ProductQuoteChange $productQuoteChange
 * @property int $productQuoteId
 * @property int|null $caseId
 */
class ProductQuoteChangeCreatedEvent
{
    public ProductQuoteChange $productQuoteChange;
    public int $productQuoteId;
    public ?int $caseId;

    public function __construct(ProductQuoteChange $productQuoteChange, int $productQuoteId, ?int $caseId)
    {
        $this->productQuoteChange = $productQuoteChange;
        $this->productQuoteId = $productQuoteId;
        $this->caseId = $caseId;
    }

    public function getId(): int
    {
        return $this->productQuoteChange->pqc_id;
    }
}
