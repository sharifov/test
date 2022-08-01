<?php

namespace src\listeners\client;

use common\components\jobs\UserTaskAssignJob;
use common\models\ClientEmail;
use common\models\ClientEmailQuery;
use modules\featureFlag\FFlag;
use src\events\client\ClientEmailEventInterface;
use Yii;

class ClientEmailAssignTaskListListener
{
    public function handle(ClientEmailEventInterface $event): void
    {
        try {
            /** @fflag FFlag::FF_KEY_LEAD_TASK_ASSIGN, Lead to task List assign checker */
            if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_LEAD_TASK_ASSIGN)) {
                $existsClientEmailValid = ClientEmailQuery::getQueryClientEmailByClientId($event->getClientEmail()->client_id)
                    ->andWhere(['<>', 'id', $event->getClientEmail()->id])->exists();

                if (!$existsClientEmailValid && $event->getClientEmail()->type !== ClientEmail::EMAIL_INVALID) {
                    $job = new UserTaskAssignJob($event->getClientEmail()->client_id);
                    \Yii::$app->queue_job->push($job);
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e, 'ClientEmailCreatedListener:handle');
        }
    }
}
