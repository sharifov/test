<?php

namespace src\listeners\lead;

use common\components\purifier\Purifier;
use common\models\Notifications;
use src\events\lead\LeadExtraQueueEvent;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogQuery;
use Yii;
use yii\helpers\Html;

/**
 * Class LeadExtraQueueNotificationsListener
 *
 */
class LeadExtraQueueNotificationsListener
{
    public function handle(LeadExtraQueueEvent $event): void
    {
        try {
            if ($ownerId = $event->getLead()->employee_id) {
                $reason = '';
                if (
                    ($leadPoorProcessingLog = LeadPoorProcessingLogQuery::getLastLeadPoorProcessingLog($event->getLead()->id)) &&
                    $description = $leadPoorProcessingLog->lpplLppd->lppd_description
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
            Yii::error($e, 'Listeners:LeadExtraQueueNotificationsListener');
        }
    }
}
