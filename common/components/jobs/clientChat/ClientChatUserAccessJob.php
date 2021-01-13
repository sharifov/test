<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\repositories\NotFoundException;
use sales\services\clientChatService\ClientChatService;

/**
 * Class ClientChatUserAccessJob
 * @package common\components\jobs\clientChat
 *
 * @property int $chatId
 */
class ClientChatUserAccessJob implements \yii\queue\JobInterface
{
    public $chatId = 0;
    /**
     * @inheritDoc
     */
    public function execute($queue)
    {

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
