<?php

namespace sales\listeners\lead;

use common\components\jobs\UpdateLeadBOJob;
use sales\events\lead\LeadCallExpertRequestEvent;
use Yii;

/**
 * Class LeadCallExpertRequestEventListener
 */
class LeadCallExpertRequestEventListener
{

    /**
     * @param LeadCallExpertRequestEvent $event
     */
    public function handle(LeadCallExpertRequestEvent $event): void
    {
        $lead = $event->lead;

        $job = new UpdateLeadBOJob();
        $job->lead_id = $lead->id;
        $jobId = Yii::$app->queue_job->priority(200)->push($job);
//        Yii::info('Lead: ' . $lead->id . ', UpdateLeadBOJob: ' . $jobId, 'info\\LeadCallExpertRequestEventListener' );
    }

}
