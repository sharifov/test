<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\repositories\NotFoundException;
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
            if (!$clientChat = ClientChat::findOne((int) $this->clientChatId)) {
                throw new NotFoundException('ClientChat not found. clientChatId (' . $this->clientChatId . ')');
            }
            if (!isset($clientChat->ccv->ccvCvd->cvd_visitor_rc_id)) {
                throw new NotFoundException('Visitor RC id is not found. clientChatId (' . $this->clientChatId . ')');
            }

            $botCloseChatResult = \Yii::$app->chatBot->endConversation(
                $clientChat->cch_rid,
                $clientChat->ccv->ccvCvd->cvd_visitor_rc_id,
                $this->shallowClose
            );
            if ($botCloseChatResult['error']) {
                $errorMessage = '[Chat Bot] ' . $botCloseChatResult['error']['message'] ?? 'Unknown error message';
                $errorMessage .= ' clientChatId (' . $this->clientChatId . ')';
                \Yii::error(
                    $errorMessage,
                    'ClientChatEndConversationJob:ChatBot:Error'
                );
                throw new \RuntimeException($errorMessage);
            }

            \Yii::info(
                'Chat Bot request successfully processed. Rid (' . $clientChat->cch_rid . ')',
                'info\ClientChatEndConversationJob:successfully'
            );
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
