<?php

namespace src\listeners\lead;

use common\components\purifier\Purifier;
use common\models\Lead;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use src\events\lead\LeadStatusChangedEvent;
use Yii;

class LeadFromSnoozeNotificationListener
{
    public function handle(LeadStatusChangedEvent $event)
    {
        if ($event->oldStatus === Lead::STATUS_SNOOZE) {
            $subject = Yii::t('leadChangesStatus', 'Lead-{id} to {status}', ['id' => $event->lead->id, 'status' => Lead::getStatus($event->newStatus)]);

            $body = Yii::t(
                'leadChangesStatus',
                "Lead status changed ({oldStatus} to {newStatus})",
                [
                    'oldStatus' => Lead::getStatus($event->oldStatus ?? 0),
                    'newStatus' => Lead::getStatus($event->newStatus),
                ]
            );

            if ($ntf = Notifications::create($event->lead->employee_id, $subject, $body, Notifications::TYPE_INFO, true)) {
                // Notifications::socket($owner->id, null, 'getNewNotification', [], true);
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $event->lead->employee_id], $dataNotification);
            } else {
                Yii::warning(
                    'Not created Email notification to employee_id: ' . $event->lead->employee_id . ', lead: ' . $event->lead->id,
                    'LeadFromSnoozeNotificationListener:sendNotification'
                );
            }
        }
    }
}
