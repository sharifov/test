<?php

namespace src\listeners\cases;

use src\entities\cases\events\CasesManualChangeStatusProcessingEvent;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use common\components\purifier\Purifier;
use common\models\Employee;
use Yii;

class CasesManualChangeStatusProcessingEventNotificationListener
{
    public function handle(CasesManualChangeStatusProcessingEvent $event): void
    {
        try {
            if (!$event->oldOwner) {
                return;
            } else {
                $userNotifyId = $event->oldOwner;
            }

            $user = Employee::findOne($event->newOwner);
            $userCreator = Employee::findOne($event->creatorId);
            $title = 'Case Re-assign';
            $description = 'Your Case (' . Purifier::createCaseShortLink($event->cases) . ') has been re-assigned to ' . $user->username . ' by ' . $userCreator->username;
            if ($ntf = Notifications::create($userNotifyId, $title, $description, Notifications::TYPE_WARNING, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $userNotifyId], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Manual Change Status Processing error', 'e' => $e->getMessage(), 'caseId' => $event->cases->cs_id], 'Listeners:CasesManualChangeStatusProcessingEventNotificationListener');
        }
    }
}
