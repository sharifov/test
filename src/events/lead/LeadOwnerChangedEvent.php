<?php

namespace src\events\lead;

use common\models\Lead;

/**
 * Class LeadOwnerChangedEvent
 *
 * @property Lead $lead
 * @property int|null $oldOwnerId
 * @property int $newOwnerId
 */
class LeadOwnerChangedEvent implements LeadableEventInterface, LeadableOwnerEventInterface
{
    public $lead;
    public $oldOwnerId;
    public $newOwnerId;

    /**
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

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getOldOwnerId(): ?int
    {
        return $this->oldOwnerId;
    }

    public function getNewOwnerId(): ?int
    {
        return $this->newOwnerId;
    }
}
