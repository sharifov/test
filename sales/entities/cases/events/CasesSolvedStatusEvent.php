<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesSolvedStatusEvent
 *
 * @property Cases $cases
 * @property int $oldStatus
 * @property int $ownerId
 */
class CasesSolvedStatusEvent
{
    public $cases;
    public $oldStatus;
    public $ownerId;

    /**
     * CasesSolvedStatusEvent constructor.
     * @param Cases $cases
     * @param int $oldStatus
     * @param int $ownerId
     */
    public function __construct(Cases $cases, int $oldStatus, int $ownerId)
    {
        $this->cases = $cases;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
    }
}