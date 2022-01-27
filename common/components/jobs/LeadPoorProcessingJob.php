<?php

namespace common\components\jobs;

use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\rules\LeadPoorProcessingRuleFactory;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

/**
 * Class LeadPoorProcessingJob
 *
 * @property int $leadId
 * @property string $ruleKey
 */
class LeadPoorProcessingJob extends BaseJob implements JobInterface
{
    public int $leadId;
    public string $ruleKey;

    public function __construct(int $leadId, string $ruleKey, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->ruleKey = $ruleKey;
        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();
        $logData = [
            'leadId' => $this->leadId,
            'ruleKey' => $this->ruleKey,
        ];

        try {
            $leadPoorProcessingService = (new LeadPoorProcessingRuleFactory($this->leadId, $this->ruleKey))->create();
            if (!$leadPoorProcessingService->checkCondition()) {
                throw new \RuntimeException('Check Condition failed');
            }
            $leadPoorProcessingService->handle();
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadPoorProcessingJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadPoorProcessingJob:execute:Throwable');
        }
    }
}
