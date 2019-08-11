<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesPendingStatusEvent
 *
 * @property Cases $cases
 * @property int|null $oldStatus
 * @property int|null $ownerId
 */
class CasesPendingStatusEvent
{
    public $cases;
    public $oldStatus;
    public $ownerId;

    /**
     * CasesPendingStatusEvent constructor.
     * @param Cases $cases
     * @param int|null $oldStatus
     * @param int|null $ownerId
     */
    public function __construct(Cases $cases, ?int $oldStatus, ?int $ownerId)
    {
        $this->cases = $cases;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
    }
}