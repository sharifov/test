<?php

namespace src\model\leadBusinessExtraQueue\service;

use common\components\jobs\LeadBusinessExtraQueueJob;
use common\components\jobs\LeadBusinessExtraQueueRemoverJob;
use common\models\EmailTemplateType;
use common\models\Lead;
use frontend\helpers\RedisHelper;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue;
use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueueQuery;
use src\model\leadBusinessExtraQueue\repository\LeadBusinessExtraQueueRepository;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogQuery;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadBusinessExtraQueueLog\repository\LeadBusinessExtraQueueLogRepository;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRuleQuery;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingDictionary;
use yii\helpers\ArrayHelper;
use Yii;

class LeadBusinessExtraQueueService
{
    public static function addLeadBusinessExtraQueueJob(
        Lead $lead,
        ?string $description = null,
        bool $isStrictFirstTime = false,
        int $priority = 100,
    ): void {
        $idKey = 'business_extra_queue_adder_' . $lead->id;
        if (RedisHelper::checkDuplicate($idKey)) {
            return;
        }

        $logData = [
            'leadId' => $lead->id,
        ];

        try {
            $job = new LeadBusinessExtraQueueJob($lead, $description, $isStrictFirstTime);
            \Yii::$app->queue_job->priority($priority)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadBusinessExtraQueueService:addLeadBusinessExtraQueueJob:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadBusinessExtraQueueService:addLeadBusinessExtraQueueJob:Throwable');
        }
    }

    public static function addToLead(Lead $lead, string $description, bool $isStrictFirstTime = false): void
    {
        try {
            if (isset($lead->offset_gmt)) {
                $offset =   $lead->offset_gmt;
                $offset = $offset[0] === '+' ? substr_replace($offset, '-', 0, 1) : substr_replace($offset, '+', 0, 1);
                $clientTime = gmdate("H:i", strtotime($offset));
            } else {
                $clientTime = $lead->getClientTime2();
                $clientTime = $clientTime->format('H:i');
            }
            $logData = [
                'leadId' => $lead->id,
                'description' => $description,
                'clientTime' => $clientTime,
            ];
            if (!LeadBusinessExtraQueueLogQuery::isLeadWasInBusinessExtraQueue($lead->id) || $isStrictFirstTime) {
                $lbeqr = LeadBusinessExtraQueueRuleQuery::getRuleByClientTime($clientTime);
            } else {
                $lbeqr = LeadBusinessExtraQueueRuleQuery::getRepeatedRule();
            }
            if ($isStrictFirstTime) {
                self::removeFromLead(
                    $lead,
                    LeadBusinessExtraQueueLogStatus::REASON_REMOVE_FROM_LEAD_DUE_TO_SYNCING_OFFSET_GMT
                );
            } else {
                $businessExtraQ = LeadBusinessExtraQueueQuery::getByLeadAndKey($lead->id, $lbeqr->lbeqr_id);
                if (isset($businessExtraQ)) {
                    return;
                }
            }
            if (isset($lbeqr)) {
                $leadBusinessExtraQueue = LeadBusinessExtraQueue::create(
                    $lead->id,
                    $lbeqr->lbeqr_id,
                    self::getExpiration($lbeqr->lbeqr_duration),
                );
                $leadBusinessExtraQueueRepository = new LeadBusinessExtraQueueRepository($leadBusinessExtraQueue);
                $leadBusinessExtraQueueRepository->save();
                $leadBusinessExtraQueueLog = LeadBusinessExtraQueueLog::create(
                    $lead->id,
                    $leadBusinessExtraQueue->lbeq_lbeqr_id,
                    $lead->employee_id,
                    LeadBusinessExtraQueueLogStatus::STATUS_CREATED,
                    $description
                );

                $leadPoorProcessingLogRepository = new LeadBusinessExtraQueueLogRepository($leadBusinessExtraQueueLog);
                $leadPoorProcessingLogRepository->save(true);
            } else {
                throw new \DomainException('Lead Business Extra Queue Rule not found');
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            /** @fflag FFlag::FF_KEY_DEBUG, Info log enable */
            if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG)) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
                \Yii::warning($message, 'LeadBusinessExtraQueueService:addToLead:Exception');
            }
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), $logData);
            \Yii::error($message, 'LeadBusinessExtraQueueService:addToLead:Throwable');
        }
    }

    private static function getExpiration(int $minutes): string
    {
        return (new \DateTimeImmutable())
            ->modify('+ ' . $minutes . ' minutes')
            ->format('Y-m-d H:i:s');
    }

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

    public static function getLeadBusinessExtraQueueByMinExpire(Lead $lead): ?LeadBusinessExtraQueue
    {
        return LeadBusinessExtraQueue::find()
            ->where([
                'lbeq_lead_id' => $lead->id,
            ])
            ->orderBy('lbeq_expiration_dt', 'ASC')
            ->limit(1)
            ->one();
    }

    public static function ffIsEnabled(): bool
    {
        /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
        return Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE);
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
}
