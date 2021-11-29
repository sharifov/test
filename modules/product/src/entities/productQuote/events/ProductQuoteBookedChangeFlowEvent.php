<?php

namespace modules\product\src\entities\productQuote\events;

/**
 * Class ProductQuoteBookedChangeFlowEvent
 */
class ProductQuoteBookedChangeFlowEvent
{
    public $productQuoteId;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $ownerId;
    public $creatorId;

    /**
     * @param int $productQuoteId
     * @param int|null $startStatusId
     * @param int|null $endStatusId
     * @param string|null $description
     * @param int|null $ownerId
     * @param int|null $creatorId
     */
    public function __construct(
        int $productQuoteId,
        ?int $startStatusId,
        ?int $endStatusId,
        ?string $description,
        ?int $ownerId = null,
        ?int $creatorId  = null
    ) {
        $this->productQuoteId = $productQuoteId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }
}
