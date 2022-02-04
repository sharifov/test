<?php

namespace src\model\leadPoorProcessing\service;

use common\components\jobs\LeadPoorProcessingJob;
use common\components\jobs\LeadPoorProcessingRemoverJob;
use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingQuery;
use src\model\leadPoorProcessing\repository\LeadPoorProcessingRepository;
use src\model\leadPoorProcessing\service\rules\LeadPoorProcessingRuleFactory;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadPoorProcessingLog\repository\LeadPoorProcessingLogRepository;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class LeadPoorProcessingService
 */
class LeadPoorProcessingService
{
    public static function fundOrCreate(
        int $leadId,
        int $dataId,
        string $expirationDt
    ): LeadPoorProcessing {
        if (!$leadPoorProcessing = LeadPoorProcessingQuery::getByLeadAndKey($leadId, $dataId)) {
            $leadPoorProcessing = LeadPoorProcessing::create($leadId, $dataId, $expirationDt);
        }
        $leadPoorProcessing->lpp_expiration_dt = $expirationDt;

        if (!$leadPoorProcessing->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadPoorProcessing, ' '));
        }

        $leadPoorProcessingRepository = new LeadPoorProcessingRepository($leadPoorProcessing);
        $leadPoorProcessingRepository->save();
        return $leadPoorProcessingRepository->getModel();
    }

    public static function removeFromLead(Lead $lead, ?string $description = null): int
    {
        $removedCount = 0;
        if ($leadsPoorProcessing = LeadPoorProcessingQuery::getAllByLeadId($lead->id)) {
            foreach ($leadsPoorProcessing as $leadPoorProcessing) {
                $logData = [
                    'leadId' => $lead->id,
                    'lppdId' => $leadPoorProcessing->lpp_lppd_id
                ];
                try {
                    $lastPoorProcessingLog = LeadPoorProcessingLogQuery::getLastLeadPoorProcessingLog($lead->id);
                    $leadPoorProcessingLog = LeadPoorProcessingLog::create(
                        $lead->id,
                        $leadPoorProcessing->lpp_lppd_id,
                        $lastPoorProcessingLog->lppl_owner_id ?? $lead->employee_id,
                        LeadPoorProcessingLogStatus::STATUS_DELETED,
                        $description
                    );

                    $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
                    $leadPoorProcessingLogRepository->save(true);

                    if ($leadPoorProcessing->delete()) {
                        $removedCount++;
                    }
                } catch (\RuntimeException | \DomainException $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
                    \Yii::warning($message, 'LeadPoorProcessingService:removeFromLead:Exception');
                } catch (\Throwable $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
                    \Yii::error($message, 'LeadPoorProcessingService:removeFromLead:Throwable');
                }
            }
        }
        return $removedCount;
    }

    public static function removeFromLeadAndKey(Lead $lead, string $dataKey, ?string $description = null): void
    {
        $logData = [
            'leadId' => $lead->id,
            'dataKey' => $dataKey
        ];

        try {
            if (!$leadPoorProcessingData = LeadPoorProcessingDataQuery::getRuleByKey($dataKey)) {
                throw new \RuntimeException('Rule not found by key(' . $dataKey . ')');
            }
            if (!$leadPoorProcessing = LeadPoorProcessingQuery::getByLeadAndKey($lead->id, $leadPoorProcessingData->lppd_id)) {
                throw new \RuntimeException('LeadPoorProcessing not found (' . $lead->id . '/' . $leadPoorProcessingData->lppd_id . ')');
            }

            $lastPoorProcessingLog = LeadPoorProcessingLogQuery::getLastLeadPoorProcessingLog($lead->id);
            $leadPoorProcessingLog = LeadPoorProcessingLog::create(
                $lead->id,
                $leadPoorProcessing->lpp_lppd_id,
                $lastPoorProcessingLog->lppl_owner_id ?? $lead->employee_id,
                LeadPoorProcessingLogStatus::STATUS_DELETED,
                $description
            );

            $leadPoorProcessing->delete();

            $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
            $leadPoorProcessingLogRepository->save(true);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
            \Yii::info($message, 'info\LeadPoorProcessingService:removeFromLeadAndKey:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
            \Yii::error($message, 'LeadPoorProcessingService:removeFromLeadAndKey:Throwable');
        }
    }

    public static function addLeadPoorProcessingJob(
        int $leadId,
        string $dataKey,
        ?string $description = null,
        int $priority = 100
    ): void {
        $logData = [
            'leadId' => $leadId,
            'dataKey' => $dataKey,
        ];

        try {
            self::checkAbacAccess($leadId);
            $job = new LeadPoorProcessingJob($leadId, $dataKey, $description);
            \Yii::$app->queue_job->priority($priority)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadPoorProcessingService:addLeadPoorProcessingJob:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadPoorProcessingService:addLeadPoorProcessingJob:Throwable');
        }
    }

    public static function addLeadPoorProcessingRemoverJob(
        int $leadId,
        array $dataKeys,
        ?string $description = null,
        int $priority = 100
    ): void {
        $logData = [
            'leadId' => $leadId,
            'dataKeys' => $dataKeys,
        ];
        try {
            self::checkAbacAccess($leadId);
            $job = new LeadPoorProcessingRemoverJob($leadId, $dataKeys, $description);
            \Yii::$app->queue_job->priority($priority)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::info($message, 'info\LeadPoorProcessingService:addLeadPoorProcessingRemoverJob:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadPoorProcessingService:addLeadPoorProcessingRemoverJob:Throwable');
        }
    }

    private static function checkAbacAccess(int $leadId): void
    {
        if (Yii::$app->id !== 'app-frontend') {
            throw new \RuntimeException('Abac access is failed');
        }
        if (!$lead = Lead::find()->where(['id' => $leadId])->limit(1)->one()) {
            throw new \RuntimeException('Lead not found by ID(' . $leadId . ')');
        }
        if (!$employee = Employee::find()->where(['id' => $lead->employee_id])->limit(1)->one()) {
            throw new \RuntimeException('LeadOwner not found by ID(' . $lead->employee_id . ')');
        }

        /** @abac $leadAbacDto, LeadAbacObject::LOGIC_POOR_PROCESSING, LeadAbacObject::ACTION_ACCESS, add to LeadPoorProcessingJob */
        $leadAbacDto = new LeadAbacDto($lead, (int) $lead->employee_id);
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::LOGIC_POOR_PROCESSING, LeadAbacObject::ACTION_ACCESS, $employee)) {
            throw new \RuntimeException('Abac access is failed. (' . LeadAbacObject::LOGIC_POOR_PROCESSING . '/' . LeadAbacObject::ACTION_ACCESS . ')');
        }
    }
}
