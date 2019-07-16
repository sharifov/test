<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadStatusChangedEvent
 * @property Lead $lead
 * @property Lead $oldStatus
 * @property Lead $newStatus
 * @property Lead $employeeId
 */
class LeadStatusChangedEvent
{
    public $lead;
    public $oldStatus;
    public $newStatus;
    public $employeeId;

    public function __construct(Lead $lead, $oldStatus, $newStatus, $employeeId)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->employeeId = $employeeId;
    }
}
