<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadFollowUpEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $oldOwnerId
 * @property string|null $description
 * @property string $created
 */
class LeadFollowUpEvent
{

    public $lead;
    public $oldStatus;
    public $oldOwnerId;
    public $description;
    public $created;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int|null $oldOwnerId
     * @param string|null $description
     */
    public function __construct(Lead $lead, ?int $oldStatus, ?int $oldOwnerId, ?string $description)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
        $this->description = $description;
        $this->created = date('Y-m-d H:i:s');
    }

}
