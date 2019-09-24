<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadOwnerChangedEvent
 * @property Lead $lead
 * @property int|null $oldOwnerId
 * @property int $newOwnerId
 */
class LeadOwnerChangedEvent
{
    public $lead;
    public $oldOwnerId;
    public $newOwnerId;

    /**
     * LeadOwnerChangedEvent constructor.
     * @param Lead $lead
     * @param int|null $oldOwnerId
     * @param int $newOwnerId
     */
    public function __construct(Lead $lead, ?int $oldOwnerId, int $newOwnerId)
    {
        $this->lead = $lead;
        $this->oldOwnerId = $oldOwnerId;
        $this->newOwnerId = $newOwnerId;
    }
}
