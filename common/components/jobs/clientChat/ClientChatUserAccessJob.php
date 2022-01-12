<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\model\clientChat\entity\ClientChat;
use src\repositories\NotFoundException;
use src\services\clientChatService\ClientChatService;

/**
 * Class ClientChatUserAccessJob
 * @package common\components\jobs\clientChat
 *
 * @property int $chatId
 */
class ClientChatUserAccessJob extends BaseJob implements \yii\queue\JobInterface
{
    public $chatId = 0;
    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();
        $service = \Yii::createObject(ClientChatService::class);

        try {
            if (!$chat = ClientChat::findOne($this->chatId)) {
                throw new NotFoundException('Chat not found by id: ' . $this->chatId);
            }

            if ($chat->isInProgress() || $chat->isInClosedStatusGroup()) {
                return;
            }

            $service->sendRequestToUsers($chat);
        } catch (NotFoundException $e) {
            \Yii::info($e->getMessage(), 'info\ClientChatUserAccessJob');
        } catch (\Throwable $e) {
            AppHelper::throwableLogger($e, 'ClientChatUserAccessJob:Execute:Throwable', false);
        }
    }
}
