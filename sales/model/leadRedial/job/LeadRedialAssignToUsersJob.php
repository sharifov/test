<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Lead;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\assign\LeadRedialMultiAssigner;
use Yii;
use yii\queue\JobInterface;

/**
 * Class LeadRedialAssignToUsersJob
 *
 * @property int $leadId
 * @property int $agentsLimit
 * @property int $retryNumber
 */
class LeadRedialAssignToUsersJob extends BaseJob implements JobInterface
{
    public const RETRY_COUNT = 10;
    public const LOCK_DELAY = 3;

    public int $leadId;

    public int $agentsLimit;

    public int $retryNumber;

    public function __construct(int $leadId, int $agentsLimit, int $retryNumber, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->leadId = $leadId;
        $this->agentsLimit = $agentsLimit;
        $this->retryNumber = $retryNumber;
    }

    public function execute($queue)
    {
        $this->executionTimeRegister();

        $lead = Lead::findOne($this->leadId);

        if ($lead) {
            try {
                $locker = new AssignUserLocker();
                if (!$locker->lock()) {
                    if ($this->retryNumber > self::RETRY_COUNT) {
                        Yii::error([
                            'message' => 'Count retry > ' . self::RETRY_COUNT,
                            'leadId' => $this->leadId,
                        ], 'LeadRedialAssignToUsersJob');
                        return;
                    }
                    Yii::$app->queue_job->delay(self::LOCK_DELAY)->push(new self($this->leadId, $this->agentsLimit, $this->retryNumber + 1));
                    return;
                }

                $isAssigned = false;
                try {
                    $assigner = Yii::createObject(LeadRedialMultiAssigner::class);
                    $isAssigned = $assigner->assign($lead, $this->agentsLimit, new \DateTimeImmutable());
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
                    Yii::$app->queue_job->delay(SettingHelper::getRedialUserAccessExpiredSecondsLimit())->push(new LeadRedialExpiredAccessJob($lead->id));
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
}
