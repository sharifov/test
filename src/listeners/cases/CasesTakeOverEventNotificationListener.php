<?php

namespace src\listeners\cases;

use src\entities\cases\events\CasesTakeOverEvent;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use common\components\purifier\Purifier;
use common\models\Employee;
use Yii;

class CasesTakeOverEventNotificationListener
{
    public function handle(CasesTakeOverEvent $event): void
    {
        try {
            $newOwner = Employee::findOne($event->newOwner);
            if (!$newOwner) {
                return;
            }
            $title = 'Case Take Over';
            $description = 'Your Case (' . Purifier::createCaseShortLink($event->cases) . ') has been taken by ' . $newOwner->username;

            if ($ntf = Notifications::create($event->oldOwner, $title, $description, Notifications::TYPE_WARNING, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $event->oldOwner], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Take Over Notification error', 'e' => $e->getMessage(), 'caseId' => $event->cases->cs_id], 'Listeners:CasesTakeOverEventNotificationListener');
        }
    }
}
