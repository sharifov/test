<?php

namespace modules\offer\src\entities\offerStatusLog;

/**
 * Class CreateDto
 *
 * @property $offerId
 * @property $startStatusId
 * @property $endStatusId
 * @property $description
 * @property $ownerId
 * @property $creatorId
 */
class CreateDto
{
    public $offerId;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $ownerId;
    public $creatorId;

    public function __construct(
        int $offerId,
        ?int $startStatusId,
        int $endStatusId,
        ?string $description,
        ?int $ownerId,
        ?int $creatorId
    )
    {
        $this->offerId = $offerId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }
}
