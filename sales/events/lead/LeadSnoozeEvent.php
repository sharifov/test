<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadPendingEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $oldOwnerId
 * @property int|null $newOwnerId
 * @property int|null $creatorId
 * @property string|null $reason
 * @property string|null $snoozeFor
 * @property string $created
 */
class LeadSnoozeEvent
{

    public $lead;
    public $oldStatus;
    public $oldOwnerId;
    public $newOwnerId;
    public $creatorId;
    public $reason;
    public $snoozeFor;
    public $created;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int|null $oldOwnerId
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     * @param string|null $snoozeFor
     */
    public function __construct(Lead $lead,
                                ?int $oldStatus,
                                ?int $oldOwnerId,
                                ?int $newOwnerId,
                                ?int $creatorId,
                                ?string $reason,
                                ?string $snoozeFor)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
        $this->newOwnerId = $newOwnerId;
        $this->creatorId = $creatorId;
        $this->reason = $reason;
        $this->snoozeFor = $snoozeFor;
        $this->created = date('Y-m-d H:i:s');
    }

}
