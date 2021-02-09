<?php

namespace sales\model\call\services;

use Yii;
use common\components\jobs\CallQueueRepeatMessageJob;
use common\models\Call;

class RepeatMessageCallJobCreator
{
    public function create(Call $call, int $depPhoneProjectId, array $repeatParams): void
    {
        if (
            !
            (!empty($repeatParams['enable']) && $repeatParams['enable'] === true && !empty($repeatParams['repeat_time']))
        ) {
            return;
        }

        $repeatJob = new CallQueueRepeatMessageJob($call->c_id, $depPhoneProjectId, microtime());
        $repeatJobId = Yii::$app->queue_job->delay((int)$repeatParams['repeat_time'])->priority(100)->push($repeatJob);
        if ($repeatJobId) {
            $call->setDataRepeat($repeatJobId, $repeatJob->departmentPhoneProjectId, $repeatJob->createdTime);
            if (!$call->save()) {
                Yii::error([
                    'message' => 'Call save error',
                    'errors' => $call->getErrors(),
                    'call' => $call->getAttributes(),
                ], 'RepeatMessageCallJobCreator::call::save');
            }
        } else {
            Yii::error([
                'message' => 'Not created Job',
                'call' => $call->getAttributes(),
            ], 'RepeatMessageCallJobCreator::JobCreate');
        }
    }
}
