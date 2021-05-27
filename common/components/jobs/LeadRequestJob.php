<?php

namespace common\components\jobs;

use sales\model\lead\useCases\lead\api\create\LeadCreateGoogleRequest;
use sales\model\leadRequest\entity\LeadRequest;
use Yii;
use yii\queue\RetryableJobInterface;

/**
 * Class LeadRequestJob
 *
 * @property $leadRequest
 */
class LeadRequestJob implements RetryableJobInterface
{
    public $leadRequest;

    public function execute($queue)
    {
        try {
            $leadCreateGoogleRequest = \Yii::$container->get(LeadCreateGoogleRequest::class);
            $leadCreateGoogleRequest->handle($this->leadRequest);
        } catch (\Throwable $throwable) {
            \Yii::error([
                'message' => $throwable->getMessage(),
                'leadRequest' => $this->leadRequest,
            ], 'LeadRequestJob:Throwable');
            throw new \Exception($throwable->getMessage());
        }
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr(): int
    {
        return 60;
    }

    /**
     * @param int $attempt number
     * @param \Exception|\Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }
}
