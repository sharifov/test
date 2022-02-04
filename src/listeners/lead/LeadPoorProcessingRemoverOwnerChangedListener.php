<?php

namespace src\listeners\lead;

use common\models\Employee;
use src\events\lead\LeadOwnerChangedEvent;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;

/**
 * Class LeadPoorProcessingRemoverOwnerChangedListener
 */
class LeadPoorProcessingRemoverOwnerChangedListener
{
    public function handle(LeadOwnerChangedEvent $event): void
    {
        try {
            $description = null;
            if (($fromName = self::getUsernameById($event->oldOwnerId)) && $toName = self::getUsernameById($event->oldOwnerId)) {
                $description = sprintf(LeadPoorProcessingLogStatus::REASON_CHANGE_OWNER, $fromName, $toName);
            }

            LeadPoorProcessingService::removeFromLead($event->getLead(), $description);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'LeadPoorProcessingRemoverOwnerChangedListener:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'LeadPoorProcessingRemoverOwnerChangedListener:Throwable');
        }
    }

    private static function getUsernameById(int $id): string
    {
        return (string) Employee::find()->select('username')->where(['id' => $id])->scalar();
    }
}
