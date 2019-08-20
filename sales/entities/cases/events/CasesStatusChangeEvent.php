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
 * @property string|null $description
 */
class CasesStatusChangeEvent
{
    public $case;
    public $toStatus;
    public $fromStatus;
    public $ownerId;
    public $description;

    /**
     * CasesStatusChangeEvent constructor.
     * @param Cases $case
     * @param int $toStatus
     * @param int|null $fromStatus
     * @param int|null $ownerId
     * @param string|null $description
     */
    public function __construct(Cases $case, int $toStatus, ?int $fromStatus, ?int $ownerId, ?string $description)
    {
        $this->case = $case;
        $this->toStatus = $toStatus;
        $this->fromStatus = $fromStatus;
        $this->ownerId = $ownerId;
        $this->description = $description;
    }

}