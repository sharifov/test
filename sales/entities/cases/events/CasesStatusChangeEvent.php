<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesStatusChangeEvent
 * @property Cases $cases
 * @property int|null $oldStatus
 * @property int $newStatus
 * @property int|null $ownerId
 */
class CasesStatusChangeEvent
{
    public $cases;
    public $oldStatus;
    public $newStatus;
    public $ownerId;

    /**
     * CasesStatusChangeEvent constructor.
     * @param Cases $cases
     * @param int|null $oldStatus
     * @param int $newStatus
     * @param int|null $ownerId
     */
    public function __construct(Cases $cases, ?int $oldStatus, int $newStatus, ?int $ownerId)
    {
        $this->cases = $cases;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->ownerId = $ownerId;
    }

}