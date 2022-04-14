<?php

namespace src\model\leadPoorProcessing\service;

use common\components\jobs\LeadFromExtraToClosedJob;
use common\models\Lead;
use src\helpers\app\AppHelper;
use src\repositories\lead\LeadRepository;
use yii\helpers\ArrayHelper;

class LeadFromExtraQueueToClosedService
{
    private $leadRepository;

    public function __construct()
    {
        $this->leadRepository = \Yii::createObject(LeadRepository::class);
    }
    public static function addLeadFromExtraToClosedJob(Lead $lead, int $priority = 100)
    {
        $logData = [
            'leadId' => $lead->id,
        ];
        try {
            $job = new LeadFromExtraToClosedJob($lead);
            \Yii::$app->queue_job->priority($priority)->push($job);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadPoorProcessingService:addLeadPoorProcessingJob:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadPoorProcessingService:addLeadPoorProcessingJob:Throwable');
        }
    }

    public function transferLeadFromExtraToClosed(Lead $lead)
    {
        $logData = [
            'leadId' => $lead->id,
        ];
        try {
            $lead->close('proper_follow_up_done_no_answer');
            $this->leadRepository->save($lead);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadPoorProcessingService:transferLeadFromExtraToClosed:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadPoorProcessingService:transferLeadFromExtraToClosed:Throwable');
        }
    }
}
