<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use sales\helpers\app\AppHelper;
use sales\model\leadRedial\assign\LeadRedialUnAssigner;
use yii\queue\JobInterface;

/**
 * Class LeadRedialExpiredAccessJob
 *
 * @property int $leadId
 */
class LeadRedialExpiredAccessJob extends BaseJob implements JobInterface
{
    public int $leadId;

    public function __construct(int $leadId, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->leadId = $leadId;
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();

        $timeStart = microtime(true);

        try {
            $unAssigner = \Yii::createObject(LeadRedialUnAssigner::class);
            $unAssigner->unAssignByLeadWithTimeExpired($this->leadId, new \DateTimeImmutable());
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Processing expired access error',
                'leadId' => $this->leadId,
                'exception' => AppHelper::throwableLog($e, false),
            ], 'LeadRedialExpiredAccessJob');
        }

        \Yii::info([
            'timePushToQueue' => $this->timeStart,
            'timeRunJob' => $timeStart,
            'timeDiffPushAndRun' => round($timeStart - $this->timeStart, 1),
            'timeExecuteJob' => round(microtime(true) - $timeStart, 1),
            'leadId' => $this->leadId,
        ], 'info\LeadRedialExpiredAccessJob');
    }
}
