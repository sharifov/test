<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadOwnerChangedEvent
 * @property Lead $lead
 * @property $oldOwnerId
 * @property $newOwnerId
 */
class LeadOwnerChangedEvent
{
    public $lead;
    public $oldOwnerId;
    public $newOwnerId;

    /**
     * LeadOwnerChangedEvent constructor.
     * @param Lead $lead
     * @param $oldOwnerId
     * @param $newOwnerId
     */
    public function __construct(Lead $lead, $oldOwnerId, $newOwnerId)
    {
        $this->lead = $lead;
        $this->oldOwnerId = $oldOwnerId;
        $this->newOwnerId = $newOwnerId;
    }
}
