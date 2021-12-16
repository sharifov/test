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

        try {
            $unAssigner = \Yii::createObject(LeadRedialUnAssigner::class);
            $unAssigner->unAssignByLeadWithTimeExpired($this->leadId, new \DateTimeImmutable());
        } catch (\Throwable $e) {
            \Yii::error(
                array_merge(
                    AppHelper::throwableLog($e, false),
                    [
                        'leadId' => $this->leadId,
                    ]
                ),
                'LeadRedialExpiredAccessJob'
            );
        }
    }
}
