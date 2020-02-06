<?php

namespace modules\order\src\entities\orderStatusLog;

/**
 * Class CreateDto
 *
 * @property $orderId
 * @property $startStatusId
 * @property $endStatusId
 * @property $description
 * @property $actionId
 * @property $ownerId
 * @property $creatorId
 */
class CreateDto
{
    public $orderId;
    public $startStatusId;
    public $endStatusId;
    public $description;
    public $actionId;
    public $ownerId;
    public $creatorId;

    public function __construct(
        int $orderId,
        ?int $startStatusId,
        int $endStatusId,
        ?string $description,
        ?int $actionId,
        ?int $ownerId,
        ?int $creatorId
    )
    {
        $this->orderId = $orderId;
        $this->startStatusId = $startStatusId;
        $this->endStatusId = $endStatusId;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }
}
