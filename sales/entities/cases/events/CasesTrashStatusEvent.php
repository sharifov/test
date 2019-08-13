<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesTrashStatusEvent
 *
 * @property Cases $cases
 * @property int $oldStatus
 * @property int|null $ownerId
 */
class CasesTrashStatusEvent
{
    public $cases;
    public $oldStatus;
    public $ownerId;

    /**
     * CasesTrashStatusEvent constructor.
     * @param Cases $cases
     * @param int $oldStatus
     * @param int|null $ownerId
     */
    public function __construct(Cases $cases, int $oldStatus, ?int $ownerId)
    {
        $this->cases = $cases;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
    }
}