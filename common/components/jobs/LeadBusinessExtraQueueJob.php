<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

class LeadBusinessExtraQueueJob extends BaseJob implements JobInterface
{
    private Lead $lead;
    private ?string $description = null;
    private bool $isStrictFirstTime;
    public function __construct(Lead $lead, ?string $description = null, bool $isStrictFirstTime = false, ?float $timeStart = null, array $config = [])
    {
        $this->lead = $lead;
        $this->description = $description;
        $this->isStrictFirstTime = $isStrictFirstTime;
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
            LeadBusinessExtraQueueService::addToLead($this->lead, $this->description, $this->isStrictFirstTime);
        } catch (\RuntimeException | \DomainException $throwable) {
            /** @fflag FFlag::FF_KEY_DEBUG, Info log enable */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG)) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::info($message, 'LeadBusinessExtraQueueJob:execute:Exception');
            }
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadBusinessExtraQueueJob:execute:Throwable');
        }
    }
}
