<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesProcessingStatusEvent
 *
 * @property Cases $case
 * @property int|null $oldStatus
 * @property int $newOwnerId
 * @property int|null $oldOwnerId
 * @property int|null $creatorId
 * @property string|null $description
 */
class CasesProcessingStatusEvent
{
    public $case;
    public $oldStatus;
    public $newOwnerId;
    public $oldOwnerId;
    public $creatorId;
    public $description;

    public function __construct(Cases $case, ?int $oldStatus, int $newOwnerId, ?int $oldOwnerId, ?int $creatorId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->newOwnerId = $newOwnerId;
        $this->oldOwnerId = $oldOwnerId;
        $this->creatorId = $creatorId;
        $this->description = $description;
    }
}
