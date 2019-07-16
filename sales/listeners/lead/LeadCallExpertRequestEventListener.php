<?php

namespace sales\listeners\lead;

use common\components\jobs\UpdateLeadBOJob;
use sales\events\lead\LeadCallExpertRequestEvent;
use Yii;

/**
 * Class LeadCallExpertRequestListener
 */
class LeadCallExpertRequestListener
{

    /**
     * @param LeadCallExpertRequestEvent $event
     */
    public function handle(LeadCallExpertRequestEvent $event): void
    {
        $lead = $event->lead;

        $job = new UpdateLeadBOJob();
        $job->lead_id = $lead->id;
        $jobId = Yii::$app->queue_job->push($job);
        // Yii::info('Lead: ' . $this->id . ', UpdateLeadBOJob: ' . $jobId, 'info\Lead:afterSave:UpdateLeadBOJob');

    }

}