<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessing\service\rules\LeadPoorProcessingRuleFactory;
use Yii;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

/**
 * Class LeadPoorProcessingRemoverJob
 *
 * @property int $leadId
 * @property array $ruleKeys
 * @property string|null $description
 */
class LeadPoorProcessingRemoverJob extends BaseJob implements JobInterface
{
    private int $leadId;
    private array $ruleKeys;
    private ?string $description = null;

    public function __construct(int $leadId, array $ruleKeys, ?string $description = null, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        $this->ruleKeys = $ruleKeys;
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
            'ruleKeys' => $this->ruleKeys,
        ];

        try {
            if (!$lead = Lead::find()->where(['id' => $this->leadId])->limit(1)->one()) {
                throw new \RuntimeException('Lead not found by ID(' . $this->leadId . ')');
            }
            foreach ($this->ruleKeys as $dataKey) {
                LeadPoorProcessingService::removeFromLeadAndKey($lead, $dataKey, $this->description);
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            /** @fflag FFlag::FF_KEY_DEBUG, Lead Poor Processing info log enable */
            if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG)) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::info($message, 'LeadPoorProcessingRemoverJob:execute:Exception');
            }
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadPoorProcessingRemoverJob:execute:Throwable');
        }
    }
}
