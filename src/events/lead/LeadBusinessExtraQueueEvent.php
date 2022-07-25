<?php

namespace src\events\lead;

use common\models\Lead;

/**
 * Class LeadBusinessExtraQueueEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $oldOwnerId
 * @property int|null $newOwnerId
 * @property int|null $creatorId
 * @property string|null $reason
 * @property string $created
 */
class LeadBusinessExtraQueueEvent implements LeadableEventInterface
{
    public $lead;
    public $oldStatus;
    public $oldOwnerId;
    public $newOwnerId;
    public $creatorId;
    public $reason;
    public $created;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int|null $oldOwnerId
     * @param int|null $newOwnerId
     * @param int|null $creatorId
     * @param string|null $reason
     */
    public function __construct(
        Lead $lead,
        ?int $oldStatus,
        ?int $oldOwnerId,
        ?int $newOwnerId,
        ?int $creatorId,
        ?string $reason
    ) {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->oldOwnerId = $oldOwnerId;
        $this->newOwnerId = $newOwnerId;
        $this->creatorId = $creatorId;
        $this->reason = $reason;
        $this->created = date('Y-m-d H:i:s');
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
