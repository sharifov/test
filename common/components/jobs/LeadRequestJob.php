<?php

namespace common\components\jobs;

use sales\model\lead\useCases\lead\api\create\LeadCreateGoogleRequest;
use sales\model\leadRequest\entity\LeadRequest;
use yii\queue\RetryableJobInterface;

/**
 * Class LeadRequestJob
 *
 * @property int|null $leadRequestId
 */
class LeadRequestJob extends BaseJob implements RetryableJobInterface
{
    public $leadRequestId;

    public function execute($queue)
    {
        $this->executionTimeRegister();
        try {
            if ($this->leadRequestId && $leadRequest = LeadRequest::findOne($this->leadRequestId)) {
                $leadCreateGoogleRequest = \Yii::$container->get(LeadCreateGoogleRequest::class);
                $leadCreateGoogleRequest->handle($leadRequest);
            }
        } catch (\Throwable $throwable) {
            \Yii::error([
                'message' => $throwable->getMessage(),
                'leadRequestId' => $this->leadRequestId,
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
