<?php

namespace sales\behaviors;

use common\components\CheckPhoneNumberJob;
use common\models\ClientPhone;
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
            ActiveRecord::EVENT_AFTER_INSERT => 'addToJob',
            ActiveRecord::EVENT_AFTER_UPDATE => 'addToJob',
        ];
    }

    public function addToJob(): void
    {
        $clientPhone = $this->owner;
        if (is_object($clientPhone) && is_a($clientPhone, ClientPhone::class)) {
            /** @var ClientPhone $clientPhone */
            if ($clientPhone->id > 0 && $clientPhone->client_id > 0) {
                $isRenewPhoneNumber = ($clientPhone->old_phone !== '' && $clientPhone->old_phone !== $clientPhone->phone);
                if (null === $clientPhone->validate_dt || $isRenewPhoneNumber) {
                    /** @var Queue $queue */
                    $queue = \Yii::$app->queue_phone_check;
                    $job = new CheckPhoneNumberJob();
                    $job->client_id = $clientPhone->client_id;
                    $job->client_phone_id = $clientPhone->id;
                    $queue->push($job);
                }
            }
        }
    }
}
