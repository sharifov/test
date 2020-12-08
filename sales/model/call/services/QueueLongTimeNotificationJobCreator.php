<?php

namespace sales\model\call\services;

use common\components\jobs\CallQueueLongTimeNotificationJob;
use Yii;
use common\models\Call;

class QueueLongTimeNotificationJobCreator
{
    public function create(Call $call, int $depPhoneProjectId, int $delay): void
    {
        $job = new CallQueueLongTimeNotificationJob($call->c_id, $depPhoneProjectId, microtime());
        $jobId = Yii::$app->queue_job->delay($delay)->priority(100)->push($job);
        if ($jobId) {
            $data = $call->getData();
            $data->queueLongTime->jobId = $jobId;
            $data->queueLongTime->departmentPhoneId = $job->departmentPhoneProjectId;
            $data->queueLongTime->createdJobTime = $job->createdTime;
            $call->setData($data);
            if (!$call->save()) {
                Yii::error([
                    'message' => 'Call save error',
                    'errors' => $call->getErrors(),
                    'call' => $call->getAttributes(),
                ], 'QueueLongTimeNotificationJobCreator::call::save');
            }
        } else {
            Yii::error([
                'message' => 'Not created Job',
                'call' => $call->getAttributes(),
            ], 'QueueLongTimeNotificationJobCreator::JobCreate');
        }
    }
}
