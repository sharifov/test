<?php

namespace src\model\leadBusinessExtraQueue\service;

use common\models\Lead;
use src\helpers\app\AppHelper;
use src\repositories\lead\LeadRepository;
use yii\helpers\ArrayHelper;

class LeadToClosedFromBusinessExtraQueueService
{
    private $leadRepository;

    public function __construct()
    {
        $this->leadRepository = \Yii::createObject(LeadRepository::class);
    }

    public function transferLeadFromBusinessExtraToClosed(Lead $lead)
    {
        $logData = [
            'leadId' => $lead->id,
        ];
        try {
            $lead->close('proper_follow_up_done_no_answer');
            $this->leadRepository->save($lead);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadToClosedFromBusinessExtraQueueService:transferLeadFromBusinessExtraToClosed:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadToClosedFromBusinessExtraQueueService:transferLeadFromBusinessExtraToClosed:Throwable');
        }
    }
}
