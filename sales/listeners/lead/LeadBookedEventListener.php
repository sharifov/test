<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadSoldEvent;
use Yii;

/**
 * Class LeadSoldEventListener
 */
class LeadSoldEventListener
{

    public function handle(LeadSoldEvent $event): void
    {
        if ($event->lead->employee_id && !$this->sendNotification('lead-status-sold', $event->lead->employee_id)) {
            Yii::warning(
                'Not send Email notification to employee_id: ' . $event->lead->employee_id . ', lead: ' . $event->lead->id,
                self::class . ':sendNotification');
        }
    }

}