<?php

namespace src\listeners\lead\leadBusinessExtraQueue;

use common\components\purifier\Purifier;
use common\models\Notifications;
use src\events\lead\LeadBusinessExtraQueueEvent;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogQuery;
use yii\helpers\Html;

class LeadBusinessExtraQueueNotificationsListener
{
    public function handle(LeadBusinessExtraQueueEvent $event): void
    {
        try {
            if ($ownerId = $event->getLead()->employee_id) {
                $reason = '';
                if (
                    ($leadLog = LeadBusinessExtraQueueLogQuery::getLastLeadBusinessExtraQueueLog($event->getLead()->id)) &&
                    $description = $leadLog->lbeqlLbeqr->lbeqr_description
                ) {
                    $reason .= ' Reason - ' . Html::encode($description);
                }

                $message = 'Lead(' . Purifier::createLeadShortLink($event->getLead()) . ') changed status to (' .
                           $event->getLead()->getStatusName() . ').' . $reason;

                Notifications::createAndPublish(
                    $ownerId,
                    'Lead changed status',
                    $message,
                    Notifications::TYPE_INFO,
                    true
                );
            }
        } catch (\Throwable $e) {
            \Yii::error($e, 'Listeners:LeadBusinessExtraQueueNotificationsListener');
        }
    }
}
