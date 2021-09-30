<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\assign\LeadRedialUnAssigner;
use sales\model\leadRedial\entity\CallRedialUserAccess;
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

            $agentsHasAccessToCall = CallRedialUserAccess::find()
                ->select('count(crua_lead_id)')
                ->andWhere('time_to_sec(TIMEDIFF(now(), crua_created_dt)) < :limitTime', [
                    'limitTime' => SettingHelper::getRedialUserAccessExpiredSecondsLimit()
                ])
                ->andWhere(['crua_lead_id' => $this->leadId])
                ->scalar();

            $limitAgents = SettingHelper::getRedialGetLimitAgents() - (int)$agentsHasAccessToCall;

            \Yii::$app->queue_job->push(new LeadRedialAssignToUsersJob($this->leadId, $limitAgents, 0));
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Processing expired access error',
                'leadId' => $this->leadId,
                'exception' => AppHelper::throwableLog($e, false),
            ], 'LeadRedialExpiredAccessJob');
        }
    }
}
