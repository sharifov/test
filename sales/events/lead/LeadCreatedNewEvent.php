<?php

namespace sales\events\lead;

use common\models\Lead;

/**
 * Class LeadCreatedNewEvent
 *
 * @property Lead $lead
 * @property string $created
 * @property int|null $creatorId
 */
class LeadCreatedNewEvent  implements LeadableEventInterface
{
    public $lead;
    public $created;
    public $creatorId;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead, ?int $creatorId)
    {
        $this->lead = $lead;
        $this->created = date('Y-m-d H:i:s');
        $this->creatorId = $creatorId;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }
}
