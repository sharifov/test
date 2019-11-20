<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedByApi
 *
 * @property Lead $lead
 * @property string $created
 * @property string $newStatus
 */
class LeadCreatedByApiEvent implements LeadableEventInterface
{
    public $lead;
    public $created;
    public $newStatus;

    /**
     * @param Lead $lead
     * @param int $newStatus
     */
    public function __construct(Lead $lead, int $newStatus)
    {
        $this->lead = $lead;
        $this->newStatus = $newStatus;
        $this->created = date('Y-m-d H:i:s');
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
