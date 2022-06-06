<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\model\leadPoorProcessing\service\LeadFromExtraQueueToClosedService;
use src\model\leadPoorProcessing\service\rules\LeadPoorProcessingRuleFactory;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

class LeadFromExtraToClosedJob extends BaseJob implements JobInterface
{
    public Lead $lead;

    public function __construct(Lead $lead, ?float $timeStart = null, array $config = [])
    {
        $this->lead = $lead;
        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();
            $logData = [
                'leadId' => $this->lead->id,
            ];
            try {
                $service = new LeadFromExtraQueueToClosedService();
                $service->transferLeadFromExtraToClosed($this->lead);
            } catch (\RuntimeException | \DomainException $throwable) {
                /** @fflag FFlag::FF_KEY_DEBUG, Lead Poor Processing info log enable */
                if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG)) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                    \Yii::warning($message, 'LeadFromExtraToClosedJob:execute:Exception');
                }
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::error($message, 'LeadPoorProcessingJob:execute:Throwable');
            }
    }
}
