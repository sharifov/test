<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadSoldEvent;

class LeadSoldListener
{

    public function handle(LeadSoldEvent $event): void
    {
        if ($event->lead->employee_id && !$this->sendNotification('lead-status-sold', $event->lead->employee_id)) {
            Yii::warning('Not send Email notification to employee_id: ' . $event->lead->employee_id . ', lead: ' . $event->lead->id, 'Lead:afterSave:sendNotification');
        }

    }

}