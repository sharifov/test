<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\lead\src\services\LeadTaskListService;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class LeadTaskListJob
 */
class LeadTaskListJob extends BaseJob implements JobInterface
{
    private int $leadId;
    private bool $isUserChanged;

    public function __construct(int $leadId, bool $isUserChanged, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->isUserChanged = $isUserChanged;

        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        try {
            if (!$lead = Lead::find()->where(['id' => $this->leadId])->limit(1)->one()) {
                throw new \RuntimeException('Lead not found');
            }

            $leadTaskListService = new LeadTaskListService($lead);
            // $leadTaskListService-> /* TODO:: add method handler */
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            $message['isUserChanged'] = $this->isUserChanged;
            \Yii::warning($message, 'LeadTaskListJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['leadId'] = $this->leadId;
            $message['isUserChanged'] = $this->isUserChanged;
            \Yii::error($message, 'LeadTaskListJob:execute:Throwable');
        }
    }
}
