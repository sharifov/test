<?php

namespace modules\webEngage\src\service\webEngageEventData\lead;

use common\models\Lead;

/**
 * Class LeadEventDataFactory
 *
 * @property Lead $lead;
 */
class LeadEventDataFactory
{
    private Lead $lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function create(): AbstractLeadEventData
    {
        switch ($this->lead->status) {
            case Lead::STATUS_NEW:
                return new LeadCreatedEventData($this->lead);
            case Lead::STATUS_BOOKED:
                return new LeadBookedEventData($this->lead);
            case Lead::STATUS_SOLD:
                return new LeadSoldEventData($this->lead);
            case Lead::STATUS_TRASH:
                return new LeadTrashedEventData($this->lead);
        }
        throw new \RuntimeException('Lead Status (' . $this->lead->getStatusName() . ') unprocessed');
    }
}
