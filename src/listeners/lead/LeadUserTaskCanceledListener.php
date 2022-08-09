<?php

namespace src\listeners\lead;

use common\components\jobs\UserTaskCanceledJob;
use common\models\Lead;
use src\events\lead\LeadStatusChangedEvent;

class LeadUserTaskCanceledListener
{
    private const CANCELED_LEAD_STATUS = [
        Lead::STATUS_PENDING,
        Lead::STATUS_PROCESSING,
        Lead::STATUS_REJECT,
        Lead::STATUS_FOLLOW_UP,
        Lead::STATUS_ON_HOLD,
        Lead::STATUS_SOLD,
        Lead::STATUS_TRASH,
        Lead::STATUS_BOOKED,
        Lead::STATUS_SNOOZE,
        Lead::STATUS_BOOK_FAILED,
        Lead::STATUS_ALTERNATIVE,
        Lead::STATUS_NEW,
        Lead::STATUS_EXTRA_QUEUE,
        Lead::STATUS_CLOSED,
        Lead::STATUS_BUSINESS_EXTRA_QUEUE,
    ];

    public function handle(LeadStatusChangedEvent $event)
    {
        if (in_array($event->newStatus, self::CANCELED_LEAD_STATUS, true)) {
            $job = new UserTaskCanceledJob($event->getLead()->id);
            \Yii::$app->queue_job->push($job);
        }
    }
}
