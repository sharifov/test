<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesProcessingStatusEvent
 *
 * @property Cases $cases
 * @property int $oldStatus
 * @property int|null $oldOwnerId
 * @property int $newOwnerId
 */
class CasesProcessingStatusEvent
{
    public $cases;
    public $oldStatus;
    public $oldOwnerId;
    public $newOwnerId;

    /**
     * CasesProcessingStatusEvent constructor.
     * @param Cases $cases
     * @param int $oldStatus
     * @param int|null $oldOwnerId
     * @param int $newOwnerId
     */
    public function __construct(Cases $cases, int $oldStatus, ?int $oldOwnerId, int $newOwnerId)
    {
        $this->cases = $cases;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
        $this->newOwnerId = $newOwnerId;
    }
}