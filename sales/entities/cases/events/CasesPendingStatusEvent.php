<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesPendingStatusEvent
 *
 * @property Cases $case
 * @property int|null $oldStatus
 * @property int|null $ownerId
 * @property string|null $description
 */
class CasesPendingStatusEvent
{
    public $case;
    public $oldStatus;
    public $ownerId;
    public $description;

    /**
     * CasesPendingStatusEvent constructor.
     * @param Cases $case
     * @param int|null $oldStatus
     * @param int|null $ownerId
     * @param string|null $description
     */
    public function __construct(Cases $case, ?int $oldStatus, ?int $ownerId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->description = $description;
    }
}