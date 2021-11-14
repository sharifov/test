<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
use common\models\Project;
use common\models\search\LeadQcallSearch;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\assign\LeadRedialMultiAssigner;
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
        $this->executionTimeRegister();

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
                Yii::$app->queue_lead_redial->delay(SettingHelper::getRedialUserAccessExpiredSecondsLimit())->push(new LeadRedialExpiredAccessJob($lead->id));
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
