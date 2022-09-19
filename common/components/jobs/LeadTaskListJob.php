<?php

namespace common\components\jobs;

use common\models\Lead;
use common\models\query\LeadQuery;
use modules\lead\src\services\LeadTaskListService;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class LeadTaskListJob
 */
class LeadTaskListJob extends BaseJob implements JobInterface
{
    private int $leadId;
    public ?int $oldOwnerId;

    public function __construct(int $leadId, ?int $oldOwnerId, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->oldOwnerId = $oldOwnerId;

        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();
        $this->timeExecution = microtime(true);

        try {
            if (!$lead = LeadQuery::getLeadById($this->leadId)) {
                throw new \RuntimeException('Lead not found');
            }

            $leadTaskListService = new LeadTaskListService($lead, $this->oldOwnerId);
            if (!$leadTaskListService->isProcessAllowed()) {
                return;
            }

            $leadTaskListService->assign();
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            $message['oldOwnerId'] = $this->oldOwnerId;
            \Yii::warning($message, 'LeadTaskListJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            $message['oldOwnerId'] = $this->oldOwnerId;
            \Yii::error($message, 'LeadTaskListJob:execute:Throwable');
        }

        $this->execTimeRegister();
    }
}
