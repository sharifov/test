<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;
use sales\services\clientChatEndConversation\ClientChatEndConversationService;
use Yii;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

/**
 * @property int $clientChatId
 * @property bool $shallowClose
 */
class ClientChatEndConversationJob implements RetryableJobInterface
{
    public $clientChatId;
    public $shallowClose = true;

    /**
     * @param Queue $queue
     * @throws \Exception
     */
    public function execute($queue): void
    {
        try {
            if ($clientChat = ClientChatEndConversationService::endConversation($this->clientChatId, $this->shallowClose)) {
                \Yii::info(
                    'Chat Bot request successfully processed. Rid (' . $clientChat->cch_rid . ')',
                    'info\ClientChatEndConversationJob:successfully'
                );
            }
        } catch (NotFoundException $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'ClientChatEndConversationJob:Execute:Throwable'
            );
        } catch (\Throwable $throwable) {
            throw new \Exception($throwable->getMessage());
        }
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr(): int
    {
        return 2 * 60;
    }

    /**
     * @param int $attempt number
     * @param \Exception|\Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error)
    {
        return 3;
    }
}
