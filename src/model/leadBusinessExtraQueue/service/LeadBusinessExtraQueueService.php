<?php

namespace src\model\leadBusinessExtraQueue\service;

use common\components\jobs\LeadBusinessExtraQueueRemoverJob;
use common\models\Lead;
use frontend\helpers\RedisHelper;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueueQuery;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogQuery;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadBusinessExtraQueueLog\repository\LeadBusinessExtraQueueLogRepository;
use yii\helpers\ArrayHelper;
use Yii;

class LeadBusinessExtraQueueService
{
    public static function removeFromLead(Lead $lead, ?string $description = null): int
    {
        $removedCount = 0;
        if ($leadsBusinessExtraQueue = LeadBusinessExtraQueueQuery::getAllByLeadId($lead->id)) {
            foreach ($leadsBusinessExtraQueue as $leadBusinessExtraQueue) {
                $logData = [
                    'leadId' => $lead->id,
                    'lbeqrId' => $leadBusinessExtraQueue->lbeq_lbeqr_id
                ];
                try {
                    $lastBusinessExtraQueueLog = LeadBusinessExtraQueueLogQuery::getLastLeadBusinessExtraQueueLog($lead->id);
                    $leadBusinessExtraQueueLog = LeadBusinessExtraQueueLog::create(
                        $lead->id,
                        $leadBusinessExtraQueue->lbeq_lbeqr_id,
                        $lastBusinessExtraQueueLog->lbeql_owner_id ?? $lead->employee_id,
                        LeadBusinessExtraQueueLogStatus::STATUS_DELETED,
                        $description
                    );

                    $leadPoorProcessingLogRepository = new LeadBusinessExtraQueueLogRepository($leadBusinessExtraQueueLog);
                    $leadPoorProcessingLogRepository->save(true);

                    if ($leadBusinessExtraQueue->delete()) {
                        $removedCount++;
                    }
                } catch (\RuntimeException | \DomainException $throwable) {
                    /** @fflag FFlag::FF_KEY_DEBUG, Info log enable */
                    if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG)) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
                        \Yii::warning($message, 'LeadBusinessExtraQueueService:removeFromLead:Exception');
                    }
                } catch (\Throwable $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
                    \Yii::error($message, 'LeadBusinessExtraQueueService:removeFromLead:Throwable');
                }
            }
        }
        return $removedCount;
    }

    public static function addLeadBusinessExtraQueueRemoverJob(
        int $leadId,
        ?string $description = null,
        int $priority = 100
    ): void {
        /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
        if (!Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE)) {
            return;
        }

        $idKey = 'business_extra_queue_remover_' . $leadId;
        if (RedisHelper::checkDuplicate($idKey)) {
            return;
        }

        $logData = [
            'leadId' => $leadId,
            'description' => $description
        ];
        try {
            $job = new LeadBusinessExtraQueueRemoverJob($leadId, $description);
            \Yii::$app->queue_job->priority($priority)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::info($message, 'info\LeadBusinessExtraQueueService:addLeadPoorProcessingRemoverJob:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadBusinessExtraQueueService:addLeadPoorProcessingRemoverJob:Throwable');
        }
    }
}
