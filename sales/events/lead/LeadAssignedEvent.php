<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadAssignedEvent
 *
 * @property Lead $lead
 * @property $oldEmployeeId
 * @property int $newEmployeeId
 * @property $oldStatus
 * @property int $newStatus
 * @property string created
 */
class LeadAssignedEvent
{

    public $lead;
    public $oldEmployeeId;
    public $newEmployeeId;
    public $oldStatus;
    public $newStatus;
    public $created;

    /**
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
        $this->created = date('Y-m-d H:i:s');
    }

}
