<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesFollowUpStatusEvent
 *
 * @property Cases $case
 * @property int|null $oldStatus
 * @property int|null $oldOwnerId
 * @property int|null $creatorId
 * @property string|null $description
 */
class CasesFollowUpStatusEvent
{
    public $case;
    public $oldStatus;
    public $oldOwnerId;
    public $creatorId;
    public $description;

    /**
     * @param Cases $case
     * @param int|null $oldStatus
     * @param int|null $oldOwnerId
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function __construct(Cases $case, ?int $oldStatus, ?int $oldOwnerId, ?int $creatorId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
        $this->creatorId = $creatorId;
        $this->description = $description;
    }
}
