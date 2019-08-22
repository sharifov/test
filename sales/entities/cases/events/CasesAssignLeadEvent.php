<?php

namespace sales\entities\cases\events;

use sales\entities\cases\Cases;

/**
 * Class CasesAssignLeadEvent
 * @property Cases $case
 * @property int|null $oldLeadId
 * @property int $newLeadId
 */
class CasesAssignLeadEvent
{
    public $case;
    public $oldLeadId;
    public $newLeadId;

    /**
     * CasesAssignLeadEvent constructor.
     * @param Cases $case
     * @param int|null $oldLeadId
     * @param int $newLeadId
     */
    public function __construct(Cases $case, ?int $oldLeadId, int $newLeadId)
    {
        $this->case = $case;
        $this->oldLeadId = $oldLeadId;
        $this->newLeadId = $newLeadId;
    }
}