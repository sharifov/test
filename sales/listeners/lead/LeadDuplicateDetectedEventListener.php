<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadDuplicateDetectedEvent;
use Yii;

/**
 * Class LeadDuplicateDetectedEventListener
 */
class LeadDuplicateDetectedEventListener
{

    /**
     * @param LeadDuplicateDetectedEvent $event
     */
    public function handle(LeadDuplicateDetectedEvent $event): void
    {
        $lead = $event->lead;

        Yii::info('Warning: detected duplicate Lead (Origin id: ' . $lead->l_duplicate_lead_id . ', Hash: ' . $lead->l_request_hash . ')', 'info\Create:Lead:duplicate');
    }

}
