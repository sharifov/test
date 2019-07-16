<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadBookedEvent;
use Yii;

/**
 * Class LeadBookedEventListener
 */
class LeadBookedEventListener
{

    public function handle(LeadBookedEvent $event): void
    {
        if ($event->lead->employee_id && !$this->sendNotification('lead-status-booked', $event->lead->employee_id, null, $event->lead)) {
            Yii::warning(
                'Not send Email notification to employee_id: ' . $event->lead->employee_id . ', lead: ' . $event->lead->id,
                self::class . ':sendNotification'
            );
        }
    }

}