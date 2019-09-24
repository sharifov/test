<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadFollowUpEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $oldOwnerId
 */
class LeadFollowUpEvent
{
    public $lead;
    public $oldStatus;
    public $oldOwnerId;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int|null $oldOwnerId
     */
    public function __construct(Lead $lead, ?int $oldStatus, ?int $oldOwnerId)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
    }
}
