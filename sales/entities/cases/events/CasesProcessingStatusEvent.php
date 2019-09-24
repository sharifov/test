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
 * @property string|null $description
 */
class CasesProcessingStatusEvent
{
    public $case;
    public $oldStatus;
    public $newOwnerId;
    public $oldOwnerId;
    public $description;

    /**
     * CasesProcessingStatusEvent constructor.
     * @param Cases $case
     * @param int|null $oldStatus
     * @param int $newOwnerId
     * @param int|null $oldOwnerId
     * @param string|null $description
     */
    public function __construct(Cases $case, ?int $oldStatus, int $newOwnerId, ?int $oldOwnerId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->newOwnerId = $newOwnerId;
        $this->oldOwnerId = $oldOwnerId;
        $this->description = $description;
    }
}
