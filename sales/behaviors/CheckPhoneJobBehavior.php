<?php

namespace sales\behaviors;

use common\components\CheckPhoneNumberJob;
use common\models\ClientPhone;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\queue\Queue;

/**
 * Class CheckPhoneJobBehavior for model ClientPhone
 */
class CheckPhoneJobBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'insertAddToJob',
            ActiveRecord::EVENT_AFTER_UPDATE => 'updateAddToJob',
        ];
    }

    public function insertAddToJob(): void
    {
        /** @var ClientPhone $clientPhone */
        $clientPhone = $this->owner;
        if (is_object($clientPhone) && is_a($clientPhone, ClientPhone::class)) {
            $job = new CheckPhoneNumberJob();
            $job->client_id = $clientPhone->client_id;
            $job->client_phone_id = $clientPhone->id;
            Yii::$app->queue_phone_check->priority(10)->push($job);
        }
    }

    public function updateAddToJob(): void
    {
        /** @var ClientPhone $clientPhone */
        $clientPhone = $this->owner;
        if (is_object($clientPhone) && is_a($clientPhone, ClientPhone::class)) {
            if ($clientPhone->id > 0 && $clientPhone->client_id > 0) {
                if (empty($clientPhone->validate_dt) || ($clientPhone->old_phone !== $clientPhone->phone)) {
                    $job = new CheckPhoneNumberJob();
                    $job->client_id = $clientPhone->client_id;
                    $job->client_phone_id = $clientPhone->id;
                    Yii::$app->queue_phone_check->priority(100)->push($job);
                }
            }
        }
    }
}
