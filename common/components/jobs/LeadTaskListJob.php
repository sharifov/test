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
    public ?int $newOwnerId;

    public function __construct(int $leadId, ?int $newOwnerId, ?int $oldOwnerId, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->newOwnerId = $newOwnerId;
        $this->oldOwnerId = $oldOwnerId;

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

            $leadTaskListService = new LeadTaskListService($lead, $this->newOwnerId, $this->oldOwnerId);
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
