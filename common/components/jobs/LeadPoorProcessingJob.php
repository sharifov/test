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
 * @property string|null $description
 */
class LeadPoorProcessingJob extends BaseJob implements JobInterface
{
    public int $leadId;
    public string $ruleKey;
    private ?string $description = null;

    public function __construct(int $leadId, string $ruleKey, ?string $description = null, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->ruleKey = $ruleKey;
        $this->description = $description;
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
            $leadPoorProcessingService = (new LeadPoorProcessingRuleFactory($this->leadId, $this->ruleKey, $this->description))->create();
            if (!$leadPoorProcessingService->checkCondition()) {
                throw new \RuntimeException('Check condition failed');
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
