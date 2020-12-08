<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * @property string $rid
 * @property string|null $comment
 * @property int|null $rating
 */
class ClientChatFeedbackJob implements JobInterface
{
    public $rid;
    public $comment;
    public $rating;

    /**
     * @param Queue $queue
     */
    public function execute($queue): void
    {
        try {
            (Yii::createObject(ClientChatRequestService::class))
                ->createOrUpdateFeedback($this->rid, $this->comment, $this->rating);
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'ClientChatFeedbackJob:Execute:Throwable',
                false
            );
        }
    }
}
