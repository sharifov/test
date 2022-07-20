<?php

namespace src\listeners\lead;

use common\components\jobs\ClientReturnIndicationJob;
use src\events\lead\LeadRejectEvent;
use src\events\lead\LeadSoldEvent;
use src\helpers\app\AppHelper;
use Yii;

class LeadRejectClientReturnIndicationListener
{
    public function handle(LeadRejectEvent $event): void
    {
        try {
            $job = new ClientReturnIndicationJob($event->lead->client_id);
            Yii::$app->queue_job->priority(10)->push($job);
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'Listeners:LeadRejectClientReturnIndicationListener::objectSegment');
        }
    }
}
