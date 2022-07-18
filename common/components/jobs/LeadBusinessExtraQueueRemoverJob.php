<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

class LeadBusinessExtraQueueRemoverJob extends BaseJob implements JobInterface
{
    private int $leadId;
    private ?string $description = null;
    public function __construct(int $leadId, ?string $description = null, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
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
        ];
        try {
            if (!$lead = Lead::find()->where(['id' => $this->leadId])->limit(1)->one()) {
                throw new \RuntimeException('Lead not found by ID(' . $this->leadId . ')');
            }
            LeadBusinessExtraQueueService::removeFromLead($lead, $this->description);
        } catch (\RuntimeException | \DomainException $throwable) {
            /** @fflag FFlag::FF_KEY_DEBUG, Info log enable */
            if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG)) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
                \Yii::info($message, 'LeadBusinessExtraQueueRemoverJob:execute:Exception');
            }
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadBusinessExtraQueueRemoverJob:execute:Throwable');
        }
    }
}
