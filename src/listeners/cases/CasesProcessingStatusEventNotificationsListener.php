<?php

namespace src\listeners\cases;

use common\components\purifier\Purifier;
use common\models\Employee;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use src\entities\cases\events\CasesProcessingStatusEvent;
use Yii;
use yii\helpers\Html;

/**
 * Class CasesProcessingStatusEventNotificationsListener
 *
 */
class CasesProcessingStatusEventNotificationsListener
{
    public function handle(CasesProcessingStatusEvent $event): void
    {
        try {
            if ($event->newOwnerId !== $event->creatorId) {
                $creator = Employee::findOne($event->creatorId);
                $title = 'Title: New Case Assigned';
                $linkToCase = Purifier::createCaseShortLink($event->case);
                $message = 'Message: Case (' . $linkToCase . ') has been assigned to you by user ' . Html::encode($creator ? $creator->username : '');

                if ($ntf = Notifications::create($event->newOwnerId, $title, $message, Notifications::TYPE_WARNING, true)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $event->newOwnerId], $dataNotification);
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesProcessingStatusEventNotificationsListener');
        }
    }
}
