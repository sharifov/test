<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\ConferenceParticipant;
use sales\helpers\app\AppHelper;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class CheckClientCallJoinToConferenceJob
 * @property int $callId
 * @property string $dateTime
 */
class CheckClientCallJoinToConferenceJob implements JobInterface
{
    public int $callId;
    public string $dateTime;

    public function execute($queue)
    {
        $call = Call::find()->andWhere(['c_id' => $this->callId, 'c_status_id' => Call::STATUS_DELAY])->one();

        if (!$call) {
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
        \Yii::$app->queue_job->push($queueJob);
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
                $result = \Yii::$app->communication->cancelCall($child['c_call_sid']);
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
}
