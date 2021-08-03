<?php

namespace common\components\jobs;

use modules\flight\models\FlightRequest;
use sales\helpers\app\AppHelper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * @property int $flight_request_id
 */
class ReprotectionCreateJob extends BaseJob implements JobInterface
{
    public $flight_request_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->executionTimeRegister();
        try {
            if (!$flightRequest = FlightRequest::findOne($this->flight_request_id)) {
                throw new \RuntimeException('FlightRequest not found by (' . $this->flight_request_id . ')');
            }

            /* TODO::  */
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'ReprotectionCreateJob:throwable'
            );
        }
        return false;
    }
}
