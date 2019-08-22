<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesTrashStatusEvent
 *
 * @property Cases $case
 * @property int $oldStatus
 * @property int|null $ownerId
 * @property string|null $description
 */
class CasesTrashStatusEvent
{
    public $case;
    public $oldStatus;
    public $ownerId;
    public $description;

    /**
     * CasesTrashStatusEvent constructor.
     * @param Cases $case
     * @param int $oldStatus
     * @param int|null $ownerId
     * @param string|null $description
     */
    public function __construct(Cases $case, int $oldStatus, ?int $ownerId, ?string $description)
    {
        $this->case = $case;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->description = $description;
    }
}