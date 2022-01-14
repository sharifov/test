<?php

namespace src\events\lead;

use common\models\Lead;

/**
 * Class LeadBookedEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $oldOwnerId
 * @property int|null $newOwnerId
 * @property int|null $creatorId
 * @property string|null $reason
 * @property string $created
 */
class LeadNewEvent
{
    public $lead;
    public $oldStatus;
    public $oldOwnerId;
    public $newOwnerId;
    public $creatorId;
    public $reason;
    public $created;

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
}
