<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadBookedEvent
 *
 * @property Lead $lead
 * @property int|null $oldStatus
 * @property int|null $ownerId
 * @property string|null $description
 * @property string $created
 */
class LeadBookedEvent
{

    public $lead;
    public $oldStatus;
    public $ownerId;
    public $description;
    public $created;

    /**
     * @param Lead $lead
     * @param int|null $oldStatus
     * @param int|null $ownerId
     * @param string|null $description
     */
    public function __construct(Lead $lead, ?int $oldStatus, ?int $ownerId, ?string $description)
    {
        $this->lead = $lead;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->description = $description;
        $this->created = date('Y-m-d H:i:s');
    }

}
