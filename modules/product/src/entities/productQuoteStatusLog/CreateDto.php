<?php

namespace modules\product\src\entities\productQuoteStatusLog;

/**
 * Class CreateDto
 *
 * @property $productQuoteId
 * @property $startStatusId
 * @property $endStatusId
 * @property $description
 * @property $ownerId
 * @property $creatorId
 */
class CreateDto
{
    public $productQuoteId;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $ownerId;
    public $creatorId;

    public function __construct(
        int $productQuoteId,
        ?int $startStatusId,
        int $endStatusId,
        ?string $description,
        ?int $ownerId,
        ?int $creatorId
    )
    {
        $this->productQuoteId = $productQuoteId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }
}
