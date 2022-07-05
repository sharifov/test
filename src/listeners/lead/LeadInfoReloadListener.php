<?php

namespace src\listeners\lead;

use common\models\Notifications;
use src\events\lead\LeadableEventInterface;
use Yii;

class LeadInfoReloadListener
{
    public function handle(LeadableEventInterface $event): void
    {
        try {
            Notifications::publish('updateLeadHeader', [
                'user_id' => $event->getLead()->employee_id
            ], [
                'data' => [
                    'lead' => [
                        'id' => $event->getLead()->id,
                        'gid' => $event->getLead()->gid
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadStatusChangedListener');
        }
    }
}
