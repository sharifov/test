<?php

namespace common\components\jobs;

use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\rules\LeadPoorProcessingRuleFactory;
use Yii;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

/**
 * Class LeadPoorProcessingJob
 *
 * @property int $leadId
 * @property array $ruleKeys
 * @property string|null $description
 */
class LeadPoorProcessingJob extends BaseJob implements JobInterface
{
    public int $leadId;
    public array $ruleKeys;
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

        foreach ($this->ruleKeys as $key) {
            $logData = [
                'leadId' => $this->leadId,
                'ruleKey' => $key,
            ];
            $transaction = new Transaction(['db' => Yii::$app->db]);
            try {
                $transaction->begin();
                $leadPoorProcessingService = (new LeadPoorProcessingRuleFactory($this->leadId, $key, $this->description))->create();
                if (!$leadPoorProcessingService->checkCondition()) {
                    throw new \RuntimeException('Check condition failed');
                }
                $leadPoorProcessingService->handle();
                $transaction->commit();
            } catch (\RuntimeException | \DomainException $throwable) {
                $transaction->rollBack();
                /** @fflag FFlag::FF_KEY_DEBUG, Lead Poor Processing info log enable */
                if (Yii::$app->ff->can(FFlag::FF_KEY_DEBUG)) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                    \Yii::warning($message, 'LeadPoorProcessingJob:execute:Exception');
                }
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::error($message, 'LeadPoorProcessingJob:execute:Throwable');
            }
        }
    }
}
