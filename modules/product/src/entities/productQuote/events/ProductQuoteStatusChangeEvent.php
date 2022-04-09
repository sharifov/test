<?php

namespace modules\product\src\entities\productQuote\events;

class ProductQuoteStatusChangeEvent implements ProductQuoteStatusChangeInterface
{
    public $productQuoteId;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $actionId;
    public $ownerId;
    public $creatorId;

    public function __construct(int $productQuoteId, ?int $startStatusId, ?int $endStatusId, ?string $description, ?int $actionId, ?int $ownerId, ?int $creatorId)
    {
        $this->productQuoteId = $productQuoteId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }

    public function getId(): int
    {
        return $this->productQuoteId;
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
