<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadTaskEvent
 * @property Lead $lead
 */
class LeadAnsweredEvent
{
    public $lead;
    public $oldEmployeeId;
    public $newEmployeeId;
    public $oldStatus;
    public $newStatus;

    /**
     * LeadTaskEvent constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }
}
