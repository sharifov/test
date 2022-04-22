<?php

namespace src\model\leadPoorProcessing\service;

use common\components\jobs\LeadPoorProcessingJob;
use common\components\jobs\LeadPoorProcessingRemoverJob;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Lead;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingDictionary;
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
                    /** @fflag FFlag::FF_KEY_DEBUG, Lead Poor Processing info log enable */
                    if (Yii::$app->ff->can(FFlag::FF_KEY_DEBUG)) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
                        \Yii::warning($message, 'LeadPoorProcessingService:removeFromLead:Exception');
                    }
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
                return;
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
            /** @fflag FFlag::FF_KEY_DEBUG, Lead Poor Processing info log enable */
            if (Yii::$app->ff->can(FFlag::FF_KEY_DEBUG)) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
                \Yii::info($message, 'info\LeadPoorProcessingService:removeFromLeadAndKey:Exception');
            }
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
            \Yii::error($message, 'LeadPoorProcessingService:removeFromLeadAndKey:Throwable');
        }
    }

    public static function addLeadPoorProcessingJob(
        int $leadId,
        array $dataKeys,
        ?string $description = null,
        int $priority = 100
    ): void {
        /** @fflag FFlag::FF_LPP_ENABLE, Lead Poor Processing Enable/Disable */
        if (!Yii::$app->ff->can(FFlag::FF_KEY_LPP_ENABLE)) {
            return;
        }

        $idKey = 'adder_' . $leadId . '_' . implode('_', $dataKeys);
        if (!self::checkDuplicate($idKey)) {
            return;
        }

        $logData = [
            'leadId' => $leadId,
            'dataKeys' => $dataKeys,
        ];

        try {
            $job = new LeadPoorProcessingJob($leadId, $dataKeys, $description);
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
        /** @fflag FFlag::FF_LPP_ENABLE, Lead Poor Processing Enable/Disable */
        if (!Yii::$app->ff->can(FFlag::FF_KEY_LPP_ENABLE)) {
            return;
        }

        $idKey = 'remover_' . $leadId . '_' . implode('_', $dataKeys);
        if (!self::checkDuplicate($idKey)) {
            return;
        }

        $logData = [
            'leadId' => $leadId,
            'dataKeys' => $dataKeys,
        ];
        try {
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

    public static function checkSmsTemplate(?string $template): bool
    {
        return in_array($template, LeadPoorProcessingDictionary::SMS_TPL_OFFER_LIST, true);
    }

    public static function checkEmailTemplate(?string $templateKey): bool
    {
        if (!$tpl = EmailTemplateType::find()->where(['etp_key' => $templateKey])->limit(1)->one()) {
            throw new \RuntimeException('EmailTemplateType not found by(' . $templateKey . ')');
        }
        return (bool) ArrayHelper::getValue($tpl->etp_params_json, 'quotes.selectRequired', false);
    }

    public static function checkDuplicate(string $idKey, int $pauseSecond = 10): bool
    {
        $redis = Yii::$app->redis;
        if (!$redis->get($idKey)) {
            $redis->setex($idKey, $pauseSecond, true);
            return true;
        }
        return false;
    }
}
