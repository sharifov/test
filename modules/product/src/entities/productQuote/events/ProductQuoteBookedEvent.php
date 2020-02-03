<?php

namespace modules\product\src\entities\productQuote\events;

class ProductQuoteBookedEvent
{
    public $productQuoteId;
    public $startStatusId;
    public $description;
    public $ownerId;
    public $creatorId;

    /**
     * ProductQuoteBookedEvent constructor.
     * @param int $productQuoteId
     * @param int|null $startStatusId
     * @param string|null $description
     * @param int|null $ownerId
     * @param int|null $creatorId
     */
    public function __construct(
        int $productQuoteId,
        ?int $startStatusId,
        ?string $description,
        ?int $ownerId,
        ?int $creatorId
    ) {
        $this->productQuoteId = $productQuoteId;
        $this->startStatusId = $startStatusId;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }
}
