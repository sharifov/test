<?php

namespace sales\listeners\lead;

use common\components\jobs\WebEngageRequestJob;
use modules\webEngage\settings\WebEngageSettings;
use modules\webEngage\src\service\webEngageEventData\lead\LeadEventDictionary;
use sales\events\lead\LeadableEventInterface;
use Yii;

/**
 * Class LeadWebEngageListener
 */
class LeadWebEngageListener
{
    public function handle(LeadableEventInterface $event): void
    {
        try {
            $lead = $event->getLead();
            if (
                in_array($lead->status, LeadEventDictionary::STATUS_PROCESSED_LIST, false) &&
                (new WebEngageSettings())->isEnabled()
            ) {
                $job = new WebEngageRequestJob();
                $job->lead_id = $lead->id;
                Yii::$app->queue_job->priority(100)->push($job);
            }
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadWebEngageStatusListener');
        }
    }
}
