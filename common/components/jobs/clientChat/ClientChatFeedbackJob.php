<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use src\model\clientChatRequest\useCase\api\create\FeedbackFormBase;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * Class ClientChatFeedbackJob
 *
 * @property FeedbackFormBase $feedbackForm
 * @package common\components\jobs\clientChat
 */
class ClientChatFeedbackJob extends BaseJob implements JobInterface
{
    public $feedbackForm;

    /**
     * @param Queue $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();
        try {
            $service = Yii::createObject(ClientChatRequestService::class);
            $service->createOrUpdateFeedback($this->feedbackForm);
        } catch (\Throwable $e) {
            AppHelper::throwableLogger($e, 'ClientChatFeedbackJob:Execute:Throwable', false);
        }
    }
}
