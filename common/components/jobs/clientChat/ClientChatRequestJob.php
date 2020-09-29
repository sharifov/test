<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * @property float|int $ttr
 * @property ClientChatRequestApiForm $requestApiForm
 */
class ClientChatRequestJob extends BaseObject implements JobInterface
{
    public ClientChatRequestApiForm $requestApiForm;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {
        try {
            $clientChatRequestService = Yii::createObject(ClientChatRequestService::class);

            if ($clientChatRequest = $clientChatRequestService->createRequest($this->requestApiForm)) {
                if($jobId = $queue->getJobId()) {
                    $clientChatRequest->ccr_job_id = $jobId;
                    $clientChatRequest->save();
                }
                Yii::info('ClientChatRequest created. ID: ' . $clientChatRequest->ccr_id,
                'info\ClientChatRequestJob:Execute:Success');
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable,
            'ClientChatRequestJob:Execute:Throwable', false);
        }
        return false;
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}