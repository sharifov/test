<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\ConferenceParticipant;
use common\models\DepartmentPhoneProject;
use src\helpers\app\AppHelper;
use src\model\call\services\QueueLongTimeNotificationJobCreator;
use src\model\call\services\RepeatMessageCallJobCreator;
use src\model\department\departmentPhoneProject\entity\params\QueueLongTimeNotificationParams;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class CheckClientCallJoinToConferenceJob
 * @property int $callId
 * @property string $dateTime
 */
class CheckClientCallJoinToConferenceJob extends BaseJob implements JobInterface
{
    private const STATUS_COMPLETED = 'completed';

    public int $callId;
    public string $dateTime;

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        /** @var Call $call */
        $call = Call::find()->andWhere(['c_id' => $this->callId, 'c_status_id' => Call::STATUS_DELAY])->one();

        if (!$call) {
            return;
        }

        if (!$this->callIsActive($call->c_call_sid)) {
            $call->setStatusCompleted();
            if (!$call->save()) {
                \Yii::error(VarDumper::dumpAsString([
                    'error' => $call->getErrors()
                ]), 'CheckClientCallJoinToConferenceJob:Call:Complete:Save');
            }
            return;
        }

        $participant = ConferenceParticipant::find()
            ->andWhere(['cp_call_id' => $this->callId])
            ->andWhere([
                'OR',
                ['>', 'cp_join_dt', $this->dateTime],
                ['>', 'cp_leave_dt', $this->dateTime],
            ])
            ->exists();

        if ($participant) {
            return;
        }

        $call->setStatusQueue();
        $call->c_created_user_id = null;

        if (!$call->save()) {
            \Yii::error(VarDumper::dumpAsString([
                'error' => $call->getErrors()
            ]), 'CheckClientCallJoinToConferenceJob:Call:Save');
            return;
        }

        $this->cancelRingingCalls($call->c_id);

        $queueJob = new CallUserAccessJob();
        $queueJob->call_id = $call->c_id;
        $queueJob->isExceptUsers = false;
        $jobId = \Yii::$app->queue_job->push($queueJob);

        $data = $call->getData();
        if (!$data->repeat->isEmpty()) {
            $this->createRepeatMessageJob($jobId, $call, $data->repeat->departmentPhoneId);
        }
        if (!$data->queueLongTime->isEmpty()) {
            $this->createCallQueueLongTimeJob($jobId, $call, $data->queueLongTime->departmentPhoneId);
        }
    }

    private function createRepeatMessageJob($jobId, $call, $depPhoneId): void
    {
        try {
            if (!$jobId) {
                throw new \DomainException('Not created CallUserAccessJob');
            }
            $depPhone = DepartmentPhoneProject::findOne($depPhoneId);
            if (!$depPhone) {
                throw new \DomainException('Not found DepartmentPhoneProject ID:' . $depPhoneId);
            }
            $dParams = @json_decode($depPhone->dpp_params, true);
            $repeatParams = $dParams['queue_repeat'] ?? [];
            if ($repeatParams) {
                (new RepeatMessageCallJobCreator())->create($call, $depPhone->dpp_id, $repeatParams);
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Create repeat call job Error.',
                'useCase' => 'CheckClientCallJoinToConferenceJob',
                'error' => $e->getMessage(),
                'call' => $call->getAttributes(),
            ], 'CallQueueRepeatMessageJob::create');
        }
    }

    private function createCallQueueLongTimeJob($jobId, $call, $depPhoneId): void
    {
        try {
            if (!$jobId) {
                throw new \DomainException('Not created CallUserAccessJob');
            }
            $depPhone = DepartmentPhoneProject::findOne($depPhoneId);
            if (!$depPhone) {
                throw new \DomainException('Not found DepartmentPhoneProject ID:' . $depPhoneId);
            }
            $dParams = @json_decode($depPhone->dpp_params, true);
            $queueLongTimeParams = new QueueLongTimeNotificationParams($dParams['queue_long_time_notification'] ?? []);
            if ($queueLongTimeParams->isActive()) {
                (new QueueLongTimeNotificationJobCreator())->create($call, $depPhone->dpp_id, $queueLongTimeParams->getDelay());
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Create call long queue time job Error.',
                'useCase' => 'CheckClientCallJoinToConferenceJob',
                'error' => $e->getMessage(),
                'call' => $call->getAttributes(),
            ], 'CallQueueLongTimeNotificationJob::create');
        }
    }

    public function cancelRingingCalls(int $callId): void
    {
        $children = Call::find()
            ->ringing()
            ->andWhere(['c_parent_id' => $callId])
            ->andWhere(['>', 'c_updated_dt', $this->dateTime])
            ->asArray()
            ->all();

        foreach ($children as $child) {
            try {
                $result = \Yii::$app->comms->cancelCall($child['c_call_sid']);
                if ($result['error']) {
                    \Yii::error(VarDumper::dumpAsString([
                        'result' => $result,
                        'child' => $child,
                    ]), 'CheckClientCallJoinToConferenceJob:hangUpRingingCalls:HangUpResult');
                } else {
                    \Yii::info(VarDumper::dumpAsString(['childId' => $child['c_id']]), 'info\CheckClientCallJoinToConferenceJob:completeRingingCall');
                }
            } catch (\Throwable $e) {
                \Yii::error(VarDumper::dumpAsString([
                    'error' => AppHelper::throwableFormatter($e),
                    'child' => $child,
                ]), 'CheckClientCallJoinToConferenceJob:hangUpRingingCalls:HangUp');
            }
        }
    }

    private function callIsActive(string $callSid): bool
    {
        try {
            $result = \Yii::$app->comms->getCallInfo($callSid);
            if ($result['error']) {
                \Yii::error(VarDumper::dumpAsString([
                    'result' => $result,
                    'callSid' => $callSid,
                ]), 'CheckClientCallJoinToConferenceJob:getCallInfo:Result');
            } else {
                if (!empty($result['result']) && $result['result']['status'] !== self::STATUS_COMPLETED) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            \Yii::error(VarDumper::dumpAsString([
                'error' => AppHelper::throwableFormatter($e),
                'callSid' => $callSid,
            ]), 'CheckClientCallJoinToConferenceJob:getCallInfo:Throwable');
        }
        return false;
    }
}
