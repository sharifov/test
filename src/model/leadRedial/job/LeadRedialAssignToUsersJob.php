<?php

namespace src\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
use common\models\Project;
use common\models\search\LeadQcallSearch;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\leadRedial\assign\LeadRedialMultiAssigner;
use Yii;
use yii\queue\JobInterface;

/**
 * Class LeadRedialAssignToUsersJob
 *
 * @property int $leadId
 */
class LeadRedialAssignToUsersJob extends BaseJob implements JobInterface
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

        $callRedialSearch = new LeadQcallSearch();
        $leadQcall = $callRedialSearch
            ->searchRedialLeadsToBeAssign(
                [
                    $callRedialSearch->formName() => [
                        'lqc_lead_id' => $this->leadId,
                    ]
                ],
                array_keys(Project::getList()),
                new \DateTimeImmutable()
            )
            ->asArray()
            ->one();

        if (!$leadQcall) {
            return;
        }

        try {
            $lead = Lead::findOne($leadQcall['lqc_lead_id']);
            if (!$lead) {
                return;
            }

            $isAssigned = false;
            try {
                $limitUsers = SettingHelper::getRedialGetLimitAgents() - (int)$leadQcall['agentsHasAccessToCall'];

                if ($limitUsers < 1) {
                    return;
                }

                $assigner = Yii::createObject(LeadRedialMultiAssigner::class);
                $isAssigned = $assigner->assign($lead, $limitUsers, new \DateTimeImmutable());
            } catch (\Throwable $e) {
                Yii::error(
                    array_merge(
                        AppHelper::throwableLog($e, false),
                        [
                            'leadId' => $this->leadId,
                        ]
                    ),
                    'LeadRedialAssignToUsersJob'
                );
            }

            if ($isAssigned) {
                $job = new LeadRedialExpiredAccessJob($lead->id);
                $delay = SettingHelper::getRedialUserAccessExpiredSecondsLimit();
                $job->delayJob = $delay;
                Yii::$app->queue_lead_redial->delay($delay)->push($job);
            }
        } catch (\Throwable $e) {
            Yii::error(
                array_merge(
                    AppHelper::throwableLog($e, false),
                    [
                        'leadId' => $this->leadId,
                    ]
                ),
                'LeadRedialAssignToUsersJob'
            );
        }
    }
}
