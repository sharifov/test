<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadSoldEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $ownerId
 */
class LeadSoldEvent
{
    public $lead;
    public $oldStatus;
    public $ownerId;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int|null $ownerId
     */
    public function __construct(Lead $lead, ?int $oldStatus, ?int $ownerId)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
    }
}
