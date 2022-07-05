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
    private bool $isNewOwner;

    public function __construct(int $leadId, bool $isNewOwner = true, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->isNewOwner = $isNewOwner;

        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        try {
            if (!$lead = LeadQuery::getLeadById($this->leadId)) {
                throw new \RuntimeException('Lead not found');
            }

            $leadTaskListService = new LeadTaskListService($lead, $this->isNewOwner);
            if (!$leadTaskListService->isProcessAllowed()) {
                return;
            }

            $leadTaskListService->assign();
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            $message['isNewOwner'] = $this->isNewOwner;
            \Yii::warning($message, 'LeadTaskListJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            $message['isNewOwner'] = $this->isNewOwner;
            \Yii::error($message, 'LeadTaskListJob:execute:Throwable');
        }
    }
}
