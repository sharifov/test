<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesSolvedStatusEvent
 *
 * @property Cases $case
 * @property int $oldStatus
 * @property int $ownerId
 * @property string|null $description
 */
class CasesSolvedStatusEvent
{
    public $case;
    public $oldStatus;
    public $ownerId;
    public $description;

    /**
     * CasesSolvedStatusEvent constructor.
     * @param Cases $case
     * @param int $oldStatus
     * @param int $ownerId
     * @param string|null $description
     */
    public function __construct(Cases $case, int $oldStatus, int $ownerId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->description = $description;
    }
}