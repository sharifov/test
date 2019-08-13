<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesFollowUpStatusEvent
 *
 * @property Cases $cases
 * @property int $oldStatus
 * @property int|null $oldOwnerId
 */
class CasesFollowUpStatusEvent
{
    public $cases;
    public $oldStatus;
    public $oldOwnerId;

    /**
     * CasesFollowUpStatusEvent constructor.
     * @param Cases $cases
     * @param int $oldStatus
     * @param int|null $oldOwnerId
     */
    public function __construct(Cases $cases, int $oldStatus, ?int $oldOwnerId)
    {
        $this->cases = $cases;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
    }
}