<?php

namespace src\listeners\cases;

use src\entities\cases\events\CasesOwnerChangeEvent;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use common\components\purifier\Purifier;
use common\models\Employee;
use Yii;

class CasesOwnerChangeEventLogListener
{
    public function handle(CasesOwnerChangeEvent $event): void
    {
        try {
            $user = Employee::findOne($event->newOwner);
            if (!$event->oldOwner) {
                $user_notify_id = $event->newOwner;
            } else {
                $user_notify_id = $event->oldOwner;
            }
            if ($event->creatorId && $event->creatorId !== $event->newOwner) {
                $user_creator = Employee::findOne($event->creatorId);
                $title = 'Case Re-assign';
                $description = 'Your Case (' . Purifier::createCaseShortLink($event->cases) . ') has been re-assigned to ' . $user->username . ' by ' . $user_creator->username;
            } else {
                $title = 'Case Take Over';
                $description = 'Your Case (' . Purifier::createCaseShortLink($event->cases) . ') has been taken by ' . $user->username;
            }
            if ($ntf = Notifications::create($user_notify_id, $title, $description, Notifications::TYPE_WARNING, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $user_notify_id], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Status from Awaiting to Solved error', 'e' => $e->getMessage(), 'caseId' => $event->cases->cs_id], 'Listeners:CasesSwitchStatusAwaitingtoSolvedListener');
        }
    }
}
