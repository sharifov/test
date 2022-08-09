<?php

namespace src\listeners\lead;

use common\components\jobs\UserTaskCanceledJob;
use common\models\Lead;
use src\events\lead\LeadStatusChangedEvent;

class LeadUserTaskCanceledListener
{
    private const CANCELED_LEAD_STATUS = [
        Lead::STATUS_TRASH
    ];

    public function handle(LeadStatusChangedEvent $event)
    {
        if (in_array($event->newStatus, self::CANCELED_LEAD_STATUS, true)) {
            $job = new UserTaskCanceledJob($event->getLead()->id);
            \Yii::$app->queue_job->push($job);
        }
    }
}
