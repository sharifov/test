<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedBookFailedEvent
 *
 * @property Lead $lead
 * @property int $status
 * @property string $created
 */
class LeadCreatedBookFailedEvent implements LeadableEventInterface
{
    public $lead;
    public $status;
    public $created;

    public function __construct(Lead $lead, int $status)
    {
        $this->lead = $lead;
        $this->status = $status;
        $this->created = date('Y-m-d H:i:s');
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
