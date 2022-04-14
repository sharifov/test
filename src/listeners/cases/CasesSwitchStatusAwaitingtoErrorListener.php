<?php

namespace src\listeners\cases;

use src\entities\cases\events\CasesErrorStatusEvent;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use common\components\purifier\Purifier;
use src\entities\cases\CasesStatus;
use Yii;

class CasesSwitchStatusAwaitingtoErrorListener
{
    public function handle(CasesErrorStatusEvent $event): void
    {
        try {
            if ($event->oldStatus == CasesStatus::STATUS_AWAITING && $ntf = Notifications::create($event->ownerId, 'Case Error', 'Case (' . Purifier::createCaseShortLink($event->case) . ') has been moved from Awaiting to Error', Notifications::TYPE_WARNING, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $event->ownerId], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Status from Awaiting to Error error', 'e' => $e->getMessage(), 'caseId' => $event->case->cs_id], 'Listeners:CasesSwitchStatusAwaitingtoErrorListener');
        }
    }
}
