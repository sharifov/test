<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\lead\src\services\LeadTaskListService;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

class UserTaskCanceledJob extends BaseJob implements JobInterface
{
    private int $leadId;

    public function __construct(int $leadId, $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;

        parent::__construct($timeStart, $config);
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();

        try {
            $lead = $this->getLead();
            if (!$lead) {
                throw new \RuntimeException('Lead not found');
            }
            $leadTaskListService = new LeadTaskListService($lead);
            $leadTaskListService->canceledAllUserTask();
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            \Yii::warning($message, 'UserTaskCanceledJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            \Yii::error($message, 'UserTaskCanceledJob:execute:Throwable');
        }
    }

    private function getLead(): ?Lead
    {
        return Lead::findOne($this->leadId);
    }
}
