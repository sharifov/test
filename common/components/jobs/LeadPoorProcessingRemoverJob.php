<?php

namespace common\components\jobs;

use common\models\Lead;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessing\service\rules\LeadPoorProcessingRuleFactory;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

/**
 * Class LeadPoorProcessingRemoverJob
 *
 * @property int $leadId
 * @property array $ruleKeys
 */
class LeadPoorProcessingRemoverJob extends BaseJob implements JobInterface
{
    public int $leadId;
    public array $ruleKeys;

    public function __construct(int $leadId, array $ruleKeys, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->ruleKeys = $ruleKeys;
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
            'ruleKeys' => $this->ruleKeys,
        ];

        try {
            if (!$lead = Lead::find()->where(['id' => $this->leadId])->limit(1)->one()) {
                throw new \RuntimeException('Lead not found by ID(' . $this->leadId . ')');
            }
            foreach ($this->ruleKeys as $dataKey) {
                LeadPoorProcessingService::removeFromLeadAndKey($lead, $dataKey);
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::info($message, 'LeadPoorProcessingRemoverJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadPoorProcessingRemoverJob:execute:Throwable');
        }
    }
}
