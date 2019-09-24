<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedCloneByUserEvent
 *
 * @property Lead $lead
 * @property int $ownerId
 */
class LeadCreatedCloneByUserEvent
{
    public $lead;
    public $ownerId;

    /**
     * @param Lead $lead
     * @param int $ownerId
     */
    public function __construct(Lead $lead, int $ownerId)
    {
        $this->lead = $lead;
        $this->ownerId = $ownerId;
    }
}
