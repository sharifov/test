<?php

namespace src\model\leadPoorProcessing\service;

use common\models\Lead;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingQuery;
use src\model\leadPoorProcessing\repository\LeadPoorProcessingRepository;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadPoorProcessingLog\repository\LeadPoorProcessingLogRepository;
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

    public static function removeFromLead(Lead $lead): int
    {
        $removedCount = 0;
        if ($leadsPoorProcessing = LeadPoorProcessingQuery::getAllByLeadId($lead->id)) {
            foreach ($leadsPoorProcessing as $leadPoorProcessing) {
                $logData = [
                    'leadId' => $lead->id,
                    'lppdId' => $leadPoorProcessing->lpp_lppd_id
                ];
                try {
                    $leadPoorProcessingLog = LeadPoorProcessingLog::create(
                        $lead->id,
                        $leadPoorProcessing->lpp_lppd_id,
                        $lead->employee_id,
                        LeadPoorProcessingLogStatus::STATUS_DELETED
                    );

                    $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
                    $leadPoorProcessingLogRepository->save(true);

                    $leadPoorProcessing->delete();
                    $removedCount++;
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
}
