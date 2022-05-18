<?php

namespace src\listeners\cases;

use src\entities\cases\events\CasesMultipleChangeStatusProcessingEvent;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use common\components\purifier\Purifier;
use common\models\Employee;
use Yii;

class CasesMultipleChangeStatusProcessingEventNotificationListener
{
    public function handle(CasesMultipleChangeStatusProcessingEvent $event): void
    {
        try {
            if (!$event->oldOwner) {
                return;
            }

            $user = Employee::findOne($event->newOwner);
            $userCreator = Employee::findOne($event->creatorId);
            $title = 'Case Re-assign';
            $description = 'Your Case (' . Purifier::createCaseShortLink($event->cases) . ') has been re-assigned to ' . $user->username . ' by ' . $userCreator->username;
            if ($ntf = Notifications::create($event->oldOwner, $title, $description, Notifications::TYPE_WARNING, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $event->oldOwner], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Multiple Change Status Processing Notification error', 'e' => $e->getMessage(), 'caseId' => $event->cases->cs_id], 'Listeners:CasesMultipleChangeStatusProcessingEventNotificationListener');
        }
    }
}
