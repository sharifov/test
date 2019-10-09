<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCallStatusChangeEvent
 *
 * @property Lead $lead
 * @property int|null $oldCallStatus
 * @property int $newCallStatus
 * @property int|null $ownerId
 */
class LeadCallStatusChangeEvent
{

    public $lead;
    public $oldCallStatus;
    public $newCallStatus;
    public $ownerId;

    /**
     * @param Lead $lead
     * @param int|null $oldCallStatus
     * @param int $newCallStatus
     * @param int|null $ownerId
     */
    public function __construct(Lead $lead, ?int $oldCallStatus, int $newCallStatus, ?int $ownerId)
    {
        $this->lead = $lead;
        $this->oldCallStatus = $oldCallStatus;
        $this->newCallStatus = $newCallStatus;
        $this->ownerId = $ownerId;
    }

}
