<?php

namespace src\listeners\lead;

use common\models\LeadCallExpert;
use src\events\lead\LeadCallExpertChangedEvent;
use src\helpers\app\AppHelper;
use Yii;
use yii\helpers\ArrayHelper;

class LeadCallExpertChangedListener
{
    /**
     * @param LeadCallExpertChangedEvent $event
     * @return void
     */
    public function handle(LeadCallExpertChangedEvent $event): void
    {
        try {
            LeadCallExpert::updateAll(
                ['lce_status_id' => LeadCallExpert::STATUS_DONE],
                [
                    'lce_lead_id' => $event->leadId,
                    'lce_status_id' => LeadCallExpert::STATUS_PENDING
                ]
            );
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), ['lead_id' => $event->leadId]);
            Yii::warning($message, 'LeadCallExpertChangedListener::handle::Throwable');
        }
    }
}
