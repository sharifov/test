<?php

namespace src\listeners\cases;

use src\entities\cases\events\CasesSolvedStatusEvent;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use common\components\purifier\Purifier;
use src\entities\cases\CasesStatus;
use Yii;

class CasesSwitchStatusAwaitingtoSolvedListener
{
    public function handle(CasesSolvedStatusEvent $event): void
    {
        try {
            $subject = Yii::t('email', 'Case Solved');
            $body = Yii::t(
                'email',
                "Case ({case_link}) has been moved from Awaiting to Solved.",
                [
                    'case_link' => Purifier::createCaseShortLink($event->case)
                ]
            );
            if ($event->oldStatus == CasesStatus::STATUS_AWAITING && $ntf = Notifications::create($event->ownerId, $subject, $body, Notifications::TYPE_SUCCESS, true)) {
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $event->ownerId], $dataNotification);
            }
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Status from Awaiting to Solved error', 'e' => $e->getMessage(), 'caseId' => $event->case->cs_id], 'Listeners:CasesSwitchStatusAwaitingtoSolvedListener');
        }
    }
}
