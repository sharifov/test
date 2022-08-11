<?php

namespace src\listeners\client;

use common\components\jobs\UserTaskAssignJob;
use common\models\ClientPhone;
use common\models\query\ClientPhoneQuery;
use modules\featureFlag\FFlag;
use src\events\client\ClientPhoneEventInterface;
use Yii;

class ClientPhoneAssignTaskListListener
{
    public function handle(ClientPhoneEventInterface $event): void
    {
        try {
            /** @fflag FFlag::FF_KEY_LEAD_TASK_ASSIGN, Lead to task List assign checker */
            if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_LEAD_TASK_ASSIGN)) {
                $existsClientPhoneValid = ClientPhoneQuery::getQueryClientPhoneByClientId($event->getClientPhone()->client_id)
                    ->andWhere(['<>', 'id', $event->getClientPhone()->id])->exists();

                if (!$existsClientPhoneValid && (int)$event->getClientPhone()->type !== ClientPhone::PHONE_INVALID) {
                    $job = new UserTaskAssignJob($event->getClientPhone()->client_id);
                    \Yii::$app->queue_job->push($job);
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e, 'ClientPhoneCreatedListener:handle');
        }
    }
}
