<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestFeedbackSubForm;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * @property float|int $ttr
 * @property ClientChatRequestFeedbackSubForm $feedbackForm
 */
class ClientChatFeedbackJob extends BaseObject implements JobInterface
{
    public ClientChatRequestFeedbackSubForm $feedbackForm;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {
        try {
            $clientChatRequestService = Yii::createObject(ClientChatRequestService::class);

            if ($feedback = $clientChatRequestService->createOrUpdateFeedback($this->feedbackForm)) {
                Yii::info('Feedback created. ID: ' . $feedback->ccf_id,
                'info\ClientChatFeedbackJob:Execute:Success');
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable,
            'ClientChatFeedbackJob:Execute:Throwable', false);
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