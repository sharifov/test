<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadAssignedEvent
 * @property Lead $lead
 * @property $oldEmployeeId
 * @property int $newEmployeeId
 * @property $oldStatus
 * @property int $newStatus
 */
class LeadAssignedEvent
{
    public $lead;
    public $oldEmployeeId;
    public $newEmployeeId;
    public $oldStatus;
    public $newStatus;

    /**
     * LeadAssignedEvent constructor.
     * @param Lead $lead
     * @param $oldEmployeeId
     * @param int $newEmployeeId
     * @param $oldStatus
     * @param int $newStatus
     */
    public function __construct(Lead $lead, $oldEmployeeId, int $newEmployeeId, $oldStatus, int $newStatus)
    {
        $this->lead = $lead;
        $this->oldEmployeeId = $oldEmployeeId;
        $this->newEmployeeId = $newEmployeeId;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
