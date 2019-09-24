<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadProcessingEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int $newOwnerId
 * @property int|null $oldOwnerId
 */
class LeadProcessingEvent
{

    public $lead;
    public $oldStatus;
    public $newOwnerId;
    public $oldOwnerId;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int $newOwnerId
     * @param int|null $oldOwnerId
     */
    public function __construct(Lead $lead, ?int $oldStatus, int $newOwnerId, ?int $oldOwnerId)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->newOwnerId = $newOwnerId;
        $this->oldOwnerId = $oldOwnerId;
    }

}
