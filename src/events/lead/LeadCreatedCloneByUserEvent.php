<?php

namespace src\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedCloneByUserEvent
 *
 * @property Lead $lead
 * @property int|null $owner
 * @property int|null $ownerOfOriginalLead
 */
class LeadCreatedCloneByUserEvent implements LeadableEventInterface
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

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
