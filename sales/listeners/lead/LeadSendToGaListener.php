<?php

namespace sales\listeners\lead;

use common\components\jobs\SendLeadInfoToGaJob;
use sales\events\lead\LeadableEventInterface;
use sales\helpers\app\AppHelper;
use Yii;

/**
 * Class LeadSendToGaListener
 */
class LeadSendToGaListener
{
    /**
     * @param LeadableEventInterface $event
     */
    public function handle(LeadableEventInterface $event): void
    {
        try {
            if ($event->getLead()->isReadyForGa()) {
                $job = new SendLeadInfoToGaJob();
                $job->lead = $event->getLead();
                Yii::$app->queue_job->priority(20)->push($job);
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable),
            'LeadSendToGaListener:Throwable');
        }
    }
}
