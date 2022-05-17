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
            $user = Employee::findOne($event->newOwner);
            if (!$event->oldOwner) {
                $userNotifyId = $event->newOwner;
            } else {
                $userNotifyId = $event->oldOwner;
            }

            $user_creator = Employee::findOne($event->creatorId);
            $title = 'Case Re-assign';
            $description = 'Your Case (' . Purifier::createCaseShortLink($event->cases) . ') has been re-assigned to ' . $user->username . ' by ' . $user_creator->username;
            if ($ntf = Notifications::create($userNotifyId, $title, $description, Notifications::TYPE_WARNING, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $userNotifyId], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Status from Awaiting to Solved error', 'e' => $e->getMessage(), 'caseId' => $event->cases->cs_id], 'Listeners:CasesSwitchStatusAwaitingtoSolvedListener');
        }
    }
}
