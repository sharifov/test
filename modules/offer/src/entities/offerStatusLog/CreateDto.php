<?php

namespace modules\offer\src\entities\offerStatusLog;

/**
 * Class CreateDto
 *
 * @property $offerId
 * @property $startStatusId
 * @property $endStatusId
 * @property $description
 * @property $actionId
 * @property $ownerId
 * @property $creatorId
 */
class CreateDto
{
    public $offerId;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $actionId;
    public $ownerId;
    public $creatorId;

    public function __construct(
        int $offerId,
        ?int $startStatusId,
        int $endStatusId,
        ?string $description,
        ?int $actionId,
        ?int $ownerId,
        ?int $creatorId
    )
    {
        $this->offerId = $offerId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }
}
