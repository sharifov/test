<?php

namespace src\listeners\lead;

use common\components\ga\GaHelper;
use common\components\jobs\SendLeadInfoToGaJob;
use src\events\lead\LeadableEventInterface;
use src\helpers\app\AppHelper;
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
            $logData = [];
            $logData['event'] = 'LeadSendAnalyticEvent';
            $logData['lead'] = $event->getLead()->attributes();
            Yii::info($logData, 'AS/*');

            if (GaHelper::checkSettings(GaHelper::TYPE_LEAD) && $event->getLead()->isReadyForGa()) {
                $job = new SendLeadInfoToGaJob();
                $job->lead = $event->getLead();
                Yii::$app->queue_job->priority(20)->push($job);
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'LeadSendToGaListener:Throwable');
        }
    }
}
