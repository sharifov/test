<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesFollowUpStatusEvent
 *
 * @property Cases $case
 * @property int $oldStatus
 * @property int|null $oldOwnerId
 * @property string|null $description
 */
class CasesFollowUpStatusEvent
{
    public $case;
    public $oldStatus;
    public $oldOwnerId;
    public $description;

    /**
     * CasesFollowUpStatusEvent constructor.
     * @param Cases $case
     * @param int $oldStatus
     * @param int|null $oldOwnerId
     * @param string|null $description
     */
    public function __construct(Cases $case, int $oldStatus, ?int $oldOwnerId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
        $this->description = $description;
    }
}