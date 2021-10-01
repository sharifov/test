<?php

namespace sales\model\leadRedial\job;

use common\models\Lead;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\assign\LeadRedialMultiAssigner;
use sales\model\leadRedial\assign\LeadRedialUnAssigner;
use sales\model\leadRedial\entity\CallRedialUserAccess;
use yii\queue\JobInterface;

/**
 * Class LeadRedialExpiredAccessJob
 *
 * @property int $leadId
 */
class LeadRedialExpiredAccessJob implements JobInterface
{
    public int $leadId;

    public function __construct(int $leadId)
    {
        $this->leadId = $leadId;
    }

    public function execute($queue)
    {
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

            $assigner = \Yii::createObject(LeadRedialMultiAssigner::class);
            $isAssigned = $assigner->assign($lead, $limitAgents, new \DateTimeImmutable());
            if ($isAssigned) {
                \Yii::$app->queue_job->delay(SettingHelper::getRedialUserAccessExpiredSecondsLimit())->push(new LeadRedialExpiredAccessJob($lead->id));
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Processing expired access error',
                'leadId' => $this->leadId,
                'exception' => AppHelper::throwableLog($e, false),
            ], 'LeadRedialExpiredAccessJob');
        }
    }
}
