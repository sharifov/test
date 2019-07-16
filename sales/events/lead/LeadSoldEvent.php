<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadStatusChangedEvent
 * @property Lead $lead
 * @property $oldEmployeeId
 * @property $newEmployeeId
 * @property $oldStatus
 * @property $newStatus
 */
class LeadAssignedEvent
{
    public $lead;
    public $oldEmployeeId;
    public $newEmployeeId;
    public $oldStatus;
    public $newStatus;

    public function __construct(Lead $lead, $oldEmployeeId, $newEmployeeId, $oldStatus, $newStatus)
    {
        $this->lead = $lead;
        $this->oldEmployeeId = $oldEmployeeId;
        $this->newEmployeeId = $newEmployeeId;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
