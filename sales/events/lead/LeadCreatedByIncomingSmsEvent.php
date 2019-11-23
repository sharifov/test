<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedByIncomingSmsEvent
 *
 * @property Lead $lead
 * @property string $created
 */
class LeadCreatedByIncomingSmsEvent
{
    public $lead;
    public $created;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->created = date('Y-m-d H:i:s');
    }
}
