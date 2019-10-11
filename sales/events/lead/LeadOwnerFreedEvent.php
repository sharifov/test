<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadOwnerFreedEvent
 *
 * @property Lead $lead
 * @property int|null $oldOwnerId
 */
class LeadOwnerFreedEvent
{

    public $lead;
    public $oldOwnerId;

    /**
     * @param Lead $lead
     * @param int|null $oldOwnerId
     */
    public function __construct(Lead $lead, ?int $oldOwnerId)
    {
        $this->lead = $lead;
        $this->oldOwnerId = $oldOwnerId;
    }

}
