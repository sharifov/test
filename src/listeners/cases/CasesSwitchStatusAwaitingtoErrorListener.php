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
            $subject = Yii::t('email', 'Case Error');
            $body = Yii::t(
                'email',
                "Case ({case_link}) has been moved from Awaiting to Error.",
                [
                    'case_link' => Purifier::createCaseShortLink($event->case)
                ]
            );
            if ($event->oldStatus == CasesStatus::STATUS_AWAITING && $ntf = Notifications::create($event->ownerId, $subject, $body, Notifications::TYPE_WARNING, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $event->ownerId], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Status from Awaiting to Error error', 'e' => $e->getMessage(), 'caseId' => $event->case->cs_id], 'Listeners:CasesSwitchStatusAwaitingtoErrorListener');
        }
    }
}
