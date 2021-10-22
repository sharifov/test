<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
use common\models\LeadQcall;
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
 * @property int $retryNumber
 */
class LeadRedialAssignToUsersJob extends BaseJob implements JobInterface
{
    public const RETRY_COUNT = 10;
    public const LOCK_DELAY = 3;

    public int $leadId;

    public int $retryNumber;

    public function __construct(int $leadId, int $retryNumber, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->leadId = $leadId;
        $this->retryNumber = $retryNumber;
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

            $locker = new AssignUserLocker();
            if (!$locker->lock($this->leadId, new \DateTimeImmutable())) {
                if ($this->retryNumber > self::RETRY_COUNT) {
                    Yii::error([
                        'message' => 'Count retry > ' . self::RETRY_COUNT,
                        'leadId' => $this->leadId,
                    ], 'LeadRedialAssignToUsersJob');
                    return;
                }
                Yii::info([
                    'message' => 'Restart job after lock',
                    'leadId' => $lead->id,
                    'retryNumber' => $this->retryNumber,
                ], 'info\LeadRedialAssignToUsersJob');
                Yii::$app->queue_lead_redial->delay(self::LOCK_DELAY)->push(new self($this->leadId, $this->retryNumber + 1));
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
                Yii::error([
                    'message' => 'Assign users error',
                    'error' => $e->getMessage(),
                    'leadId' => $this->leadId,
                    'exception' => AppHelper::throwableLog($e, false),
                ], 'LeadRedialAssignToUsersJob');
            }

            $locker->unlock();

            if ($isAssigned) {
                Yii::$app->queue_lead_redial->delay(SettingHelper::getRedialUserAccessExpiredSecondsLimit())->push(new LeadRedialExpiredAccessJob($lead->id));
            }
        } catch (\Throwable $e) {
            Yii::error([
                'message' => $e->getMessage(),
                'leadId' => $this->leadId,
                'exception' => AppHelper::throwableLog($e, false),
            ], 'LeadRedialAssignToUsersJob');
        }
    }
}
