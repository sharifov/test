<?php

namespace sales\listeners\lead;

use sales\events\lead\LeadFollowUpEvent;

/**
 * Class LeadFollowUpCounterListener
 */
class LeadFollowUpCounterListener
{

    /**
     * @param LeadFollowUpEvent $event
     */
    public function handle(LeadFollowUpEvent $event): void
    {
        $event->lead->updateCounters(['l_grade' => 1]);
    }

}
