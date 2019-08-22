<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesStatusChangeEvent
 *
 * @property Cases $case
 * @property int $toStatus
 * @property int|null $fromStatus
 * @property int|null $ownerId
 */
class CasesStatusChangeEvent
{
    public $case;
    public $toStatus;
    public $fromStatus;
    public $ownerId;

    /**
     * CasesStatusChangeEvent constructor.
     * @param Cases $case
     * @param int $toStatus
     * @param int|null $fromStatus
     * @param int|null $ownerId
     */
    public function __construct(Cases $case, int $toStatus, ?int $fromStatus, ?int $ownerId)
    {
        $this->case = $case;
        $this->toStatus = $toStatus;
        $this->fromStatus = $fromStatus;
        $this->ownerId = $ownerId;
     }

}