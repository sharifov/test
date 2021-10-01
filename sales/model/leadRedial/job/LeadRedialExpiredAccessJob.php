<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
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
        $this->executionTimeRegister();

        try {
            $unAssigner = \Yii::createObject(LeadRedialUnAssigner::class);
            $isUnAssigned = $unAssigner->unAssignByLeadWithTimeExpired($this->leadId, new \DateTimeImmutable());
            if (!$isUnAssigned) {
                return;
            }

            $lead = Lead::findOne($this->leadId);
            if (!$lead) {
                return;
            }

            $countUsers = (new UserCounter())->getCount($lead->id);

            if ($countUsers < 1) {
                return;
            }

            \Yii::$app->queue_lead_redial->push(new LeadRedialAssignToUsersJob($this->leadId,  0));
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Processing expired access error',
                'leadId' => $this->leadId,
                'exception' => AppHelper::throwableLog($e, false),
            ], 'LeadRedialExpiredAccessJob');
        }
    }
}
