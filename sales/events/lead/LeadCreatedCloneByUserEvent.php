<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedCloneByUserEvent
 *
 * @property Lead $lead
 * @property int|null $owner
 * @property int|null $ownerOfOriginalLead
 */
class LeadCreatedCloneByUserEvent
{
    public $lead;
    public $owner;
    public $ownerOfOriginalLead;

    public function __construct(Lead $lead, ?int $owner, ?int $ownerOfOriginalLead)
    {
        $this->lead = $lead;
        $this->owner = $owner;
        $this->ownerOfOriginalLead = $ownerOfOriginalLead;
    }
}
