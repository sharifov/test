<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesProcessingStatusEvent
 *
 * @property Cases $cases
 * @property int $oldStatus
 * @property int $newOwnerId
 * @property int|null $oldOwnerId
 */
class CasesProcessingStatusEvent
{
    public $cases;
    public $oldStatus;
    public $newOwnerId;
    public $oldOwnerId;

    /**
     * CasesProcessingStatusEvent constructor.
     * @param Cases $cases
     * @param int $oldStatus
     * @param int $newOwnerId
     * @param int|null $oldOwnerId
     */
    public function __construct(Cases $cases, int $oldStatus, int $newOwnerId, ?int $oldOwnerId)
    {
        $this->cases = $cases;
        $this->oldStatus = $oldStatus;
        $this->newOwnerId = $newOwnerId;
        $this->oldOwnerId = $oldOwnerId;
    }
}