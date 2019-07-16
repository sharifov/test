<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadStatusChangedEvent
 * @property Lead $lead
 * @property $oldStatus
 * @property int $newStatus
 * @property $employeeId
 */
class LeadStatusChangedEvent
{
    public $lead;
    public $oldStatus;
    public $newStatus;
    public $employeeId;

    /**
     * LeadStatusChangedEvent constructor.
     * @param Lead $lead
     * @param $oldStatus
     * @param int $newStatus
     * @param $employeeId
     */
    public function __construct(Lead $lead, $oldStatus, int $newStatus, $employeeId)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->employeeId = $employeeId;
    }
}
