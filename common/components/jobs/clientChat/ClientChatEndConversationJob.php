<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\repositories\NotFoundException;
use src\services\clientChat\ClientChatEndConversationService;
use Yii;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;

/**
 * @property int $clientChatId
 * @property bool $shallowClose
 */
class ClientChatEndConversationJob extends BaseJob implements RetryableJobInterface
{
    public $clientChatId;
    public $shallowClose = true;

    /**
     * @param Queue $queue
     * @throws \Exception
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();
        try {
            ClientChatEndConversationService::endConversation($this->clientChatId, $this->shallowClose);
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
    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }
}
