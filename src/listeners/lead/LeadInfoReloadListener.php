<?php

namespace src\listeners\lead;

use common\models\Notifications;
use src\events\lead\LeadExtraQueueEvent;
use src\events\lead\LeadPoorProcessingEvent;
use src\events\lead\LeadProcessingEvent;
use Yii;

class LeadInfoReloadListener
{
    public function handle(LeadProcessingEvent|LeadPoorProcessingEvent|LeadExtraQueueEvent $event): void
    {
        try {
            Notifications::publish('updateLeadHeader', [
                'user_id' => $event->lead->employee_id
            ], [
                'data' => [
                    'lead' => [
                        'id' => $event->lead->id,
                        'gid' => $event->lead->gid
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadStatusChangedListener');
        }
    }
}
