<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadStatusChangedEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int $newStatus
 * @property int|null $ownerId
 * @property string $created
 */
class LeadStatusChangedEvent
{

    public $lead;
    public $oldStatus;
    public $newStatus;
    public $ownerId;
    public $created;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int $newStatus
     * @param int|null $ownerId
     */
    public function __construct(Lead $lead, ?int $oldStatus, int $newStatus, ?int $ownerId)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->ownerId = $ownerId;
        $this->created = date('Y-m-d H:i:s');
    }

}
