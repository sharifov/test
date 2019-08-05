<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadFollowUpEvent
 * @property Lead $lead
 * @property $oldOwnerId
 */
class LeadFollowUpEvent
{
    public $lead;
    public $oldOwnerId;

    /**
     * LeadFollowUpEvent constructor.
     * @param Lead $lead
     * @param $oldOwnerId
     */
    public function __construct(Lead $lead, $oldOwnerId)
    {
        $this->lead = $lead;
        $this->oldOwnerId = $oldOwnerId;
    }
}
