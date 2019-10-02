<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadSnoozeEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $ownerId
 * @property string|null $snoozeFor
 * @property string|null $description
 * @property string $created
 */
class LeadSnoozeEvent
{

    public $lead;
    public $oldStatus;
    public $ownerId;
    public $snoozeFor;
    public $description;
    public $created;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int|null $ownerId
     * @param string|null $snoozeFor
     * @param string|null $description
     */
    public function __construct(Lead $lead, ?int $oldStatus, ?int $ownerId, ?string $snoozeFor, ?string $description)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->snoozeFor = $snoozeFor;
        $this->description = $description;
        $this->created = date('Y-m-d H:i:s');
    }

}
