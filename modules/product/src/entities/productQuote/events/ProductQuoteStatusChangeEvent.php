<?php

namespace modules\product\src\entities\productQuote\events;

use modules\product\src\entities\productQuote\ProductQuote;

class ProductQuoteStatusChangeEvent implements ProductQuoteStatusChangeInterface
{
    public $quote;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $actionId;
    public $ownerId;
    public $creatorId;

    public function __construct(ProductQuote $quote, ?int $startStatusId, ?int $endStatusId, ?string $description, ?int $actionId, ?int $ownerId, ?int $creatorId)
    {
        $this->quote = $quote;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }

    public function getId(): int
    {
        return $this->quote->pq_id;
    }

    public function getStartStatus(): ?int
    {
        return $this->startStatusId;
    }

    public function getEndStatus(): int
    {
        return $this->endStatusId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getActionId(): ?int
    {
        return $this->actionId;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }
}
