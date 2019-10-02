<?php

namespace sales\listeners\lead;

use common\components\jobs\QuickSearchInitPriceJob;
use sales\events\lead\LeadCreatedEvent;
use Yii;

/**
 * Class LeadCreatedEventListener
 */
class LeadCreatedEventListener
{

    /**
     * @param LeadCreatedEvent $event
     */
    public function handle(LeadCreatedEvent $event): void
    {
        $lead = $event->lead;

        /*$job = new QuickSearchInitPriceJob();
        $job->lead_id = $lead->id;
        $jobId = Yii::$app->queue_job->push($job);*/

//        Yii::info('Lead: ' . $event->lead->id . ', QuickSearchInitPriceJob: ' . $jobId, 'info\LeadCreatedEventListener');
    }

}
